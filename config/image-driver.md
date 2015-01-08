---
layout: default
permalink: config/image-driver/
title: Image driver
---

# Image driver

By default Glide uses the [GD](http://php.net/manual/en/book.image.php) library. However you can also use Glide with [Imagemagick](http://www.imagemagick.org/) if the [Imagick](http://php.net/manual/en/book.imagick.php) PHP extension is installed.

~~~ php
use League\Glide\Factories\Server;

// Set driver in Glide configuration
$glide = Server::create([
    'driver' => 'imagick',
]);
~~~