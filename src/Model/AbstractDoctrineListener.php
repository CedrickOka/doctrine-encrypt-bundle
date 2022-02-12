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

    public function __construct(private string $secret = '')
    {
        $this->encryptedObjects = [];
    }

    public function postFlush(EventArgs $args)
    {
        if (true === empty($this->encryptedObjects)) {
            return;
        }

        foreach ($this->encryptedObjects as $object => $encryptedProperties) {
            $reflObject = new \ReflectionObject($object);

            foreach ($encryptedProperties as $key => $value) {
                /** @var \ReflectionProperty $reflProperty */
                $reflProperty = $reflObject->getProperty($key);
                $reflProperty->setValue($object, $value);
            }

            unset($this->encryptedObjects[$object]);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $reflObject = new \ReflectionObject($object);

        /** @var \ReflectionProperty $reflProperty */
        foreach ($reflObject->getProperties() as $reflProperty) {
            $attributes = $reflProperty->getAttributes(Encrypt::class);

            if (true === empty($attributes)) {
                continue;
            }

            /** @var \ReflectionAttribute $attributes[0] */
            $arguments = $attributes[0]->getArguments();

            if (!$value = $reflProperty->getValue($object)) {
                continue;
            }

            $reflProperty->setValue($object, $this->decrypt($value, $arguments['algorithm']));
        }
    }

    protected function encryptOnFlush(object $object): bool
    {
        $encryptedProperties = [];
        $reflObject = new \ReflectionObject($object);

        /** @var \ReflectionProperty $reflProperty */
        foreach ($reflObject->getProperties() as $reflProperty) {
            $attributes = $reflProperty->getAttributes(Encrypt::class);

            if (true === empty($attributes)) {
                continue;
            }

            /** @var \ReflectionAttribute $attributes[0] */
            $arguments = $attributes[0]->getArguments();

            if (!$value = $reflProperty->getValue($object)) {
                continue;
            }

            $encryptedProperties[$reflProperty->getName()] = $value;
            $reflProperty->setValue($object, $this->encrypt($value, $arguments['algorithm']));
        }

        if (true === empty($encryptedProperties)) {
            return false;
        }

        $this->encryptedObjects[$object] = $encryptedProperties;

        return true;
    }

    protected function encrypt(string $value, string $algorithm): string
    {
        return base64_encode(openssl_encrypt($value, $algorithm, $this->secret));
    }

    protected function decrypt(string $value, string $algorithm): string
    {
        return openssl_decrypt(base64_decode($value), $algorithm, $this->secret);
    }
}
