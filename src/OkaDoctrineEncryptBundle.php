<?php

namespace Oka\Doctrine\EncryptBundle;

use Oka\Doctrine\EncryptBundle\DependencyInjection\CompilerPass\DoctrineListenerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class OkaDoctrineEncryptBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineListenerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 255);
    }
}
