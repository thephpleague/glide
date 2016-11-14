---
layout: default
title: The server
---

# The server

All the Glide configuration is managed through a central object called the `Server`. This includes the image [source location](/config/source-and-cache/) (where the original images are saved), the image [cache location](/config/source-and-cache/) (where the manipulated images are saved), the image manipulation API as well as any configuration options.

## Setup with factory

The easiest way to configure the `Server` is using the supplied factory.

~~~ php
<?php

$server = League\Glide\ServerFactory::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
]);
~~~

## Setup manually

You can also choose to instantiate the `Server` object manually. This allows finer control over what dependencies are being used. For example, if you wanted to add additional functionality to the API, you could load custom manipulators in addition to those provided with Glide.

~~~ php
<?php

// Set image source
$source = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('path/to/source/folder')
);

// Set image cache
$cache = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('path/to/cache/folder')
);

// Set image manager
$imageManager = new Intervention\Image\ImageManager([
    'driver' => 'imagick',
]);

// Set manipulators
$manipulators = [
    new League\Glide\Api\Manipulator\Orientation(),
    new League\Glide\Api\Manipulator\Rectangle(),
    new League\Glide\Api\Manipulator\Size(2000*2000),
    new League\Glide\Api\Manipulator\Brightness(),
    new League\Glide\Api\Manipulator\Contrast(),
    new League\Glide\Api\Manipulator\Gamma(),
    new League\Glide\Api\Manipulator\Sharpen(),
    new League\Glide\Api\Manipulator\Filter(),
    new League\Glide\Api\Manipulator\Blur(),
    new League\Glide\Api\Manipulator\Pixelate(),
    new League\Glide\Api\Manipulator\Output(),
];

// Set API
$api = new League\Glide\Api\Api($imageManager, $manipulators);

// Setup Glide server
$server = new League\Glide\Server($source, $cache, $api);
~~~