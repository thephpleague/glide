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
    'driver' => 'vips',
]);
~~~

## Driver options

The `driver` option also accepts an array, letting you pass additional options through to Intervention Image's `ImageManager`, such as stripping EXIF metadata from output images.

~~~ php
<?php

$server = \League\Glide\ServerFactory::create([
    'driver' => [
        'driver' => 'imagick',
        'strip' => true,
    ],
]);
~~~

See the [Intervention Image driver configuration docs](https://image.intervention.io/v4/getting-started/configuration-drivers) for the full list of available options.
