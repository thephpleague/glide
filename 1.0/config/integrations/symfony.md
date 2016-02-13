---
layout: default
title: Symfony (HttpFoundation) integration
---

# Symfony integration

If your application uses the [Symfony](https://symfony.com/) framework or anything that uses the `HttpFoundation` library, you can use the `SymfonyResponseFactory`.

## Installation

~~~ bash
composer require league/glide-symfony
~~~

## Configuration

~~~ php
<?php

use League\Glide\ServerFactory;
use League\Glide\Responses\SymfonyResponseFactory;

$server = ServerFactory::create([
    'response' => new SymfonyResponseFactory()
]);
~~~