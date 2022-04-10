# Getting Started With OkaInputHandlerBundle

This bundle help the user input high quality data into your web services REST.

## Prerequisites

The OkaInputHandlerBundle has the following requirements:

 - PHP 8.0+
 - Symfony 4.4+

## Installation

Installation is a quick (I promise!) 3 step process:

1. Download OkaInputHandlerBundle
2. Register the Bundle
3. Use bundle and enjoy!

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require coka/doctrine-encrypt-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Register the Bundle

Then, register the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project (Flex did it automatically):

```php
return [
    //...
    Oka\Doctrine\EncryptBundle\OkaDoctrineEncryptBundle::class => ['all' => true],
]
```

### Step 3: Use the bundle is simple

Now that the bundle is installed. 

```php
<?php
// App\Entity\Foo.php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oka\Doctrine\EncryptBundle\Annotation\Encrypt;

/**
 * 
 */
#[ORM\Entity]
class Foo
{
    // ...
    
    /**
     * #[ORM\Column(type: Types::TEXT)]
     *
     * @var string
     */
    #[Encrypt(algorithm: 'aes256')]
    protected $secret;
}
``` 
