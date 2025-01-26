---
layout: default
title: Image driver
---

# Image driver

By default Glide uses the [GD](http://php.net/manual/en/book.image.php) library. However you can also use Glide with [ImageMagick](http://www.imagemagick.org/) if the [Imagick](http://php.net/manual/en/book.imagick.php) PHP extension is installed or [libvips](https://github.com/libvips/php-vips).

~~~ php
<?php

$server = \League\Glide\ServerFactory::create([

    // Use GD (default)
    'driver' => 'gd',

    // Use ImageMagick
    'driver' => 'imagick',

    // Use libvips. Requires installing the `intervention/image-driver-vips` composer package.
    'driver' => \Intervention\Image\Drivers\Vips\Driver::class,
]);
~~~
