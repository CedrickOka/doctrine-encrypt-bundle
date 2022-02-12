<?php

namespace Oka\Doctrine\EncryptBundle\DependencyInjection\CompilerPass;

use Oka\Doctrine\EncryptBundle\EventListener\DoctrineMongoDBListener;
use Oka\Doctrine\EncryptBundle\EventListener\DoctrineORMListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class DoctrineListenerPass implements CompilerPassInterface
{
    private static array $doctrineDrivers = [
        'orm' => [
            'registry' => 'doctrine',
            'class' => DoctrineORMListener::class,
            'tag' => 'doctrine.event_listener',
        ],
        'mongodb' => [
            'registry' => 'doctrine_mongodb',
            'class' => DoctrineMongoDBListener::class,
            'tag' => 'doctrine_mongodb.odm.event_listener',
        ],
    ];

    public function process(ContainerBuilder $container)
    {
        foreach (static::$doctrineDrivers as $key => $dbDriver) {
            if (false === $container->hasDefinition($dbDriver['registry'])) {
                continue;
            }

            $container
                ->setDefinition(
                    sprintf('oka_doctrine_encrypt.%s.doctrine_listener', $key),
                    new Definition($dbDriver['class'], [new Parameter('oka_doctrine_encrypt.secret')])
                )
                ->addTag($dbDriver['tag'], ['event' => 'onFlush'])
                ->addTag($dbDriver['tag'], ['event' => 'postFlush'])
                ->addTag($dbDriver['tag'], ['event' => 'postLoad']);
        }
    }
}
