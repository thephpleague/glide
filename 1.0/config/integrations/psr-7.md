---
layout: default
title: Configuring Glide with Zend
---

# PSR-7 responses

Glide ships with the `PsrResponseFactory` class, allowing you to use any PSR-7 compliant library. However, since Glide only depends on the  PSR-7 interfaces, it cannot actually create the `Response` or `Stream` objects. Instead, you must provide them:

~~~ php
use League\Glide\ServerFactory;
use League\Glide\Responses\PsrResponseFactory;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

$server = ServerFactory::create([
    'response' => new PsrResponseFactory(new Response(), function ($stream) {
        return new Stream($stream);
    }),
]);
~~~

However, for simplicity, Glide provides a couple vendor specific PSR-7 adapters to make this easier:

- [Slim Framework](/1.0/config/integrations/slim/)
- [Zend](/1.0/config/integrations/zend/)