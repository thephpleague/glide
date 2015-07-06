---
layout: default
title: Responses
---

# Responses

In addition to generating manipulated images, Glide also helps with generating HTTP responses using the `getImageResponse()` method. This is recommended over the `outputImage()` method.

However, the type of response object needed depends on your application or framework. For example, you may want a PSR-7 response object if your using the Slim framework. Or, if you're using Laravel or Symfony, you may want to use an HttpFoundation object. To use the `getImageResponse()` method you must configure Glide to return the response you want.

- [PSR-7](config/responses#psr-7-responses)
- [HttpFoundation](config/responses#httpfoundation-responses)
- [CakePHP](config/responses#cakephp-responses)
- [Custom](config/responses#custom-responses)

## PSR-7 responses

Glide ships with a `PsrResponseFactory` class, allowing you to use any PSR-7 compliant library. However, since Glide only depends on the  PSR-7 interfaces, it cannot actually create the `Response` or `Stream` objects. Instead, you must provide them:

~~~ php
use League\Glide\ServerFactory;
$server = League\Glide\ServerFactory::create([
    'response' => new PsrResponseFactory(new Zend\Diactoros\Response(), function ($stream) {
        return new Zend\Diactoros\Stream($stream);
    }),
]);
~~~

However, for simplicity, Glide provides a few vendor adapters to make this easier:

~~~ php
use League\Glide\Responses\GuzzleResponseFactory;
use League\Glide\Responses\SlimResponseFactory;
use League\Glide\Responses\ZendResponseFactory;

$server = League\Glide\ServerFactory::create([
    'response' => new GuzzleResponseFactory(), // requires guzzlehttp/psr7
    'response' => new SlimResponseFactory(),   // requires slim/slim (> 3.0)
    'response' => new ZendResponseFactory(),   // requires zendframework/zend-diactoros
]);
~~~

## HttpFoundation responses

If your application uses Symfony's HttpFoundation library, you can use the `SymfonyResponseFactory`.

~~~ php
$server = League\Glide\ServerFactory::create([
    'response' => new League\Glide\Responses\SymfonyResponseFactory()
]);
~~~

## CakePHP responses

If your application uses the CakePHP framework, you can use the `CakeResponseFactory`.

~~~ php
$server = League\Glide\ServerFactory::create([
    'response' => new League\Glide\Responses\CakeResponseFactory()
]);
~~~

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