---
layout: default
title: Laravel integration
---

# Laravel integration

If your application uses the [Laravel](https://laravel.com/) framework, you can use the `LaravelResponseFactory`. Since Laravel uses `HttpFoundation` under the hood, this adapter actually extends the [Symfony adapter](/1.0/config/integrations/symfony/).

<p class="message-notice">This adapter requires Laravel 4 or newer.</p>

## Installation

~~~ bash
composer require league/glide-laravel
~~~

## Configuration

~~~ php
<?php

use League\Glide\ServerFactory;
use League\Glide\Responses\LaravelResponseFactory;

$server = ServerFactory::create([
    'response' => new LaravelResponseFactory(app('request'))
]);
~~~
