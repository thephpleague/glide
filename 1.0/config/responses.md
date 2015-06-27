---
layout: default
title: Responses
---

# Responses

In addition to generating manipulated images, Glide also helps with outputting them using HTTP responses. However, the type of response object needed depends on your app/framework. For example, you may want a PSR-7 response object if your using the Slim framework. Or, if you're using Laravel or Symfony, you may want an HttpFoundation response object. You must configure Glide to return the response you want.

## PSR-7 responses

Glide ships with a `PsrResponseFactory` class, allowing you to use any PSR-7 compliant library. However, since Glide only depends on the  PSR-7 interfaces, it cannot actually create the base response object. Instead, you must provide it.

~~~ php
use League\Glide\ServerFactory;
use League\Glide\Responses\PsrResponseFactory;

$server = ServerFactory::create([
    'response' => new PsrResponseFactory(new Slim\Http\Response())
]);
~~~

## HttpFoundation responses

If your applicaton uses Symfony's HttpFoundation library, you can use the `SymfonyResponseFactory`, which is provided as a bridge package. Start by installing it:

~~~ bash
composer require league/glide-symfony
~~~

Once you have installed the bridge, simply pass in a new instance of the factory:

~~~ php
use League\Glide\ServerFactory;
use League\Glide\Responses\PsrResponseFactory;

$server = ServerFactory::create([
    'response' => new SymfonyResponseFactory()
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

    /**
     * Send the response.
     * @param  FilesystemInterface $cache The cache file system.
     * @param  string              $path  The cached file path.
     * @return null
     */
    public function send(FilesystemInterface $cache, $path);
}
~~~