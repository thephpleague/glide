---
layout: default
title: Zend integration
---

# Zend integration

If your application uses the [Zend Diactoros](https://github.com/zendframework/zend-diactoros) package, for example within the [Zend Expressive](http://framework.zend.com/expressive), you can use the `ZendResponseFactory`.

## Installation

~~~ bash
composer require league/glide-zend
~~~

## Configuration

~~~ php
<?php

use League\Glide\ServerFactory;
use League\Glide\Responses\ZendResponseFactory;

$server = ServerFactory::create([
    'response' => new ZendResponseFactory(),
]);
~~~
