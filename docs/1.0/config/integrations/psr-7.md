---
layout: default
title: PSR-7 integration
---

# PSR-7 integration

Glide ships with the `PsrResponseFactory` class, allowing you to use any [PSR-7](http://www.php-fig.org/psr/psr-7/) compliant library. However, since Glide only depends on the PSR-7 interfaces, it cannot actually create the `Response` or `Stream` objects. Instead, you must provide them.

## Configuration

The following example uses the [Zend Diactoros](https://github.com/zendframework/zend-diactoros) library, but any PSR-7 compatible package will work.

~~~ php
<?php

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

## Vendor specific adapters

However, for simplicity, Glide provides a vendor specific PSR-7 adapters to make this easier:

- [Slim](/1.0/config/integrations/slim/)
- [Zend](/1.0/config/integrations/zend/)

## Using a PSR-17 ResponseFactory
[PSR-17](https://www.php-fig.org/psr/psr-17/) describes a common standard for factories that create PSR-7 compliant objects.  

The following example uses the [Zend Diactoros](https://github.com/http-interop/http-factory-diactoros) factory, but any PSR-17 compatible package will work.
~~~ php
<?php

use League\Glide\ServerFactory;
use League\Glide\Responses\Psr17ResponseFactory;
use Http\Factory\Diactoros\ResponseFactory;

$server = ServerFactory::create([
    'response' => new Psr17ResponseFactory(new ResponseFactory()),
]);
~~~
