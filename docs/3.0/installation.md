---
layout: default
title: Installation
---

# Installation

## Using Composer

Glide is available on [Packagist](https://packagist.org/packages/league/glide) and can be installed using [Composer](https://getcomposer.org/). This can be done by running the following command:

~~~ bash
composer require league/glide
~~~

Be sure to also include the Composer autoload file in your project:

~~~ php
<?php

require 'vendor/autoload.php';
~~~

## Framework integration

If you want a Framework specific version, the following adapters are available. Note, these adapters automatically include the base library (`league/glide`), so you don't need to require both.

~~~ bash
composer require admad/cakephp-glide
composer require league/glide-laravel
composer require league/glide-slim
composer require league/glide-symfony
composer require league/glide-zend
~~~

<p class="message-notice">See <a href="/2.0/config/responses/">responses</a> for more information about integrating with a specific framework.</p>

## Downloading .zip file

This project is also available for download as a `.zip` file on GitHub. Visit the [releases page](https://github.com/thephpleague/glide/releases), select the version you want, and click the "Source code (zip)" download button.