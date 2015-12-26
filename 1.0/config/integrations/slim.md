---
layout: default
title: Slim integration
---

# Slim integration

If your application uses [Slim Framework](http://www.slimframework.com/), you can use the `SlimResponseFactory`.

<p class="message-notice">This adapter requires Slim 3 or newer, which is based on PSR-7.</p>

## Installation

~~~ bash
composer require league/slim
~~~

## Configuration

~~~ php
use League\Glide\ServerFactory;
use League\Glide\Responses\SlimResponseFactory;

$server = League\Glide\ServerFactory::create([
    'response' => new SlimResponseFactory(),
]);
~~~