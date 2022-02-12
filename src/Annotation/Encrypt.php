<?php

namespace Oka\Doctrine\EncryptBundle\Annotation;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Encrypt
{
    public function __construct(private string $algorithm = 'aes256')
    {
    }
}
