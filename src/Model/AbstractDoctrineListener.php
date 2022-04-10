<?php

namespace Oka\Doctrine\EncryptBundle\Model;

use Doctrine\Common\EventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Oka\Doctrine\EncryptBundle\Annotation\Encrypt;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
abstract class AbstractDoctrineListener
{
    private array $encryptedObjects;
    private array $encryptedProperties;

    public function __construct(private string $secret = '')
    {
        $this->encryptedObjects = [];
        $this->encryptedProperties = [];
    }

    public function postFlush(EventArgs $args)
    {
        if (true === empty($this->encryptedObjects)) {
            return;
        }

        foreach ($this->encryptedObjects as $class => $object) {
            $reflObject = new \ReflectionObject($object);

            foreach ($this->encryptedProperties[$class] as $propertyName => $propertyValue) {
                /** @var \ReflectionProperty $reflProperty */
                $reflProperty = $reflObject->getProperty($propertyName);
                $reflProperty->setValue($object, $propertyValue);
            }

            unset($this->encryptedObjects[$object::class], $this->encryptedProperties[$object::class]);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $reflObject = new \ReflectionObject($object);

        /** @var \ReflectionProperty $reflProperty */
        foreach ($reflObject->getProperties() as $reflProperty) {
            if (!$value = $reflProperty->getValue($object)) {
                continue;
            }

            $attributes = $reflProperty->getAttributes(Encrypt::class);

            if (true === empty($attributes)) {
                continue;
            }

            /** @var \Oka\Doctrine\EncryptBundle\Annotation\Encrypt $encryptAttribute */
            $encryptAttribute = $attributes[0]->newInstance();
            $reflProperty->setValue($object, $this->decrypt($value, $encryptAttribute->algorithm, $encryptAttribute->initializationVector));
        }
    }

    protected function encryptOnFlush(object $object): bool
    {
        $reflObject = new \ReflectionObject($object);

        /** @var \ReflectionProperty $reflProperty */
        foreach ($reflObject->getProperties() as $reflProperty) {
            if (!$value = $reflProperty->getValue($object)) {
                continue;
            }

            $attributes = $reflProperty->getAttributes(Encrypt::class);

            if (true === empty($attributes)) {
                continue;
            }

            /** @var \Oka\Doctrine\EncryptBundle\Annotation\Encrypt $encryptAttribute */
            $encryptAttribute = $attributes[0]->newInstance();
            $this->encryptedProperties[$object::class][$reflProperty->getName()] = $value;
            $reflProperty->setValue($object, $this->encrypt($value, $encryptAttribute->algorithm, $encryptAttribute->initializationVector));
        }

        if (true === empty($this->encryptedProperties[$object::class])) {
            return false;
        }

        $this->encryptedObjects[$object::class] = $object;

        return true;
    }

    protected function encrypt(string $value, string $algorithm, string $initializationVector): string
    {
        return base64_encode(openssl_encrypt($value, $algorithm, $this->secret, null, $initializationVector));
    }

    protected function decrypt(string $value, string $algorithm, string $initializationVector): string
    {
        return openssl_decrypt(base64_decode($value), $algorithm, $this->secret, null, $initializationVector);
    }
}
