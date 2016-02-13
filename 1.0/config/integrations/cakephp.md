---
layout: default
title: CakePHP integration
---

# CakePHP integration

If your application uses the [CakePHP](http://cakephp.org/) framework, you can use the `CakeResponseFactory`.

<p class="message-notice">This adapter requires CakePHP 3 or newer.</p>

## Installation

~~~ bash
composer require league/glide-cake
~~~

## Configuration

~~~ php
<?php

use League\Glide\ServerFactory;
use League\Glide\Responses\CakeResponseFactory;

$server = ServerFactory::create([
    'response' => new CakeResponseFactory()
]);
~~~