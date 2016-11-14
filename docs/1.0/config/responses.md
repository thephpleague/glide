---
layout: default
title: Responses
---

# Responses

In addition to generating manipulated images, Glide also helps with creating HTTP responses using the `getImageResponse()` method. This is recommended over the `outputImage()` method, since it allows your application to handle the actual output of the image.

However, the type of response object needed depends on your application or framework. For example, you may want a [PSR-7](http://www.php-fig.org/psr/psr-7/) response object if your using the Slim framework. Or, if you're using Laravel or Symfony, you may want to use an [HttpFoundation](http://symfony.com/doc/current/components/http_foundation/introduction.html) object. To use the `getImageResponse()` method you must configure Glide to return the response you want.

## Response integrations

| Vendor                                       | Message interface   | Adapter package            |
|----------------------------------------------|---------------------|----------------------------|
| [PSR-7](/1.0/config/integrations/psr-7/)     | PSR-7               | *Included in base package* |
| [CakePHP](/1.0/config/integrations/cakephp/) | *Vendor specific*   | league/glide-cake          |
| [Laravel](/1.0/config/integrations/laravel/) | HttpFoundation      | league/glide-laravel       |
| [Slim](/1.0/config/integrations/slim/)       | PSR-7               | league/glide-slim          |
| [Symfony](/1.0/config/integrations/symfony/) | HttpFoundation      | league/glide-symfony       |
| [Zend](/1.0/config/integrations/zend/)       | PSR-7               | league/glide-zend          |

## Custom responses

If your particular project doesn't use PSR-7 or HttpFoundation, or if you'd like finer control over how your response objects are created, you can use your own response factories. Glide provides the `ResponseFactoryInterface` interface for this.

~~~ php
<?php

namespace League\Glide\Responses;

use League\Flysystem\FilesystemInterface;

interface ResponseFactoryInterface
{
    /**
     * Create the response.
     * @param  FilesystemInterface $cache The cache file system.
     * @param  string              $path  The cached file path.
     * @return mixed               The response object.
     */
    public function create(FilesystemInterface $cache, $path);
}
~~~