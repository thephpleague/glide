---
layout: default
title: Slim integration
---

# Slim integration

If your application uses [Slim](http://www.slimframework.com/) framework, you can use the `SlimResponseFactory`.

<p class="message-notice">This adapter requires Slim 3 or newer, which is based on PSR-7.</p>

## Installation

~~~ bash
composer require league/glide-slim
~~~

## Configuration

~~~ php
<?php

use League\Glide\ServerFactory;
use League\Glide\Responses\SlimResponseFactory;

$server = ServerFactory::create([
    'response' => new SlimResponseFactory(),
]);
~~~
