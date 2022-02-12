<?php

namespace Oka\Doctrine\EncryptBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class OkaDoctrineEncryptExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('oka_doctrine_encrypt.secret', $config['secret']);
    }
}
