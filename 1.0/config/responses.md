---
layout: default
title: Responses
---

# Responses

In addition to generating manipulated images, Glide also helps with creating HTTP responses using the `getImageResponse()` method. This is recommended over the `outputImage()` method, since it allows your application to handle the actual output of the image.

However, the type of response object needed depends on your application or framework. For example, you may want a PSR-7 response object if your using the Slim framework. Or, if you're using Laravel or Symfony, you may want to use an HttpFoundation object. To use the `getImageResponse()` method you must configure Glide to return the response you want.

## Response integrations

- [PSR-7](/1.0/config/integrations/psr-7/)
- [CakePHP](/1.0/config/integrations/cakephp/)
- [Laravel](/1.0/config/integrations/laravel/)
- [Slim Framework](/1.0/config/integrations/slim/)
- [Symfony](/1.0/config/integrations/symfony/) (HttpFoundation)
- [Zend](/1.0/config/integrations/zend/)

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