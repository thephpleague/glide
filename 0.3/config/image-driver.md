---
layout: default
title: Image driver
---

# Image driver

By default Glide uses the [GD](http://php.net/manual/en/book.image.php) library. However you can also use Glide with [ImageMagick](http://www.imagemagick.org/) if the [Imagick](http://php.net/manual/en/book.imagick.php) PHP extension is installed.

~~~ php
<?php

$server = League\Glide\ServerFactory::create([
    'driver' => 'imagick',
]);
~~~