---
layout: default
title: Symfony (HttpFoundation) integration
---

# Symfony integration

If your application uses the Symfony framework or anything that uses `HttpFoundation`, you can use the `SymfonyResponseFactory`.

## Installation

~~~ bash
composer require league/glide-symfony
~~~

## Configuration

If your application uses Symfony's HttpFoundation library, you can use the `SymfonyResponseFactory`.

~~~ php
$server = League\Glide\ServerFactory::create([
    'response' => new League\Glide\Responses\SymfonyResponseFactory()
]);
~~~