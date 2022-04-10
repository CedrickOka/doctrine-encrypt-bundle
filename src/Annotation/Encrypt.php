<?php

namespace Oka\Doctrine\EncryptBundle\Annotation;

use Attribute;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Encrypt
{
    public function __construct(public string $algorithm = 'aes256', public string $initializationVector = '0000000000000000')
    {
    }
}
