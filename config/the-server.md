---
layout: default
permalink: config/the-server/
title: The server
---

# The server

All the Glide configuration is managed through a central object call the `Server`. This includes the image [source location](/config/source-and-cache/) (where the original images are saved), the image [cache location](/config/source-and-cache/) (where the manipulated images are saved), the image manipulation API and the [sign key](/config/secure-images/) (used to secure URLs).

## Setup with factory

The easiest way to configure the `Server` is using the supplied factory.

~~~ php
$glide = League\Glide\Factories\Server::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
]);
~~~

## Setup manually

You can also choose to instantiate the `Server` object manually. This allows finer control over what dependencies are being used. For example, if you wanted to add additional functionality to the API, you could load custom manipulators in addition to those provided with Glide.

~~~ php
// Set image source
$source = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('path/to/source/folder')
);

// Set image cache
$cache = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('path/to/cache/folder')
);

// Set sign key
$signKey = new League\Glide\SignKey('your-sign-key');

// Set image manager
$imageManager = new Intervention\Image\ImageManager();

// Set manipulators
$manipulators = [
    new League\Glide\Manipulators\Orientation(),
    new League\Glide\Manipulators\Rectangle(),
    new League\Glide\Manipulators\Size(2000*2000),
    new League\Glide\Manipulators\Brightness(),
    new League\Glide\Manipulators\Contrast(),
    new League\Glide\Manipulators\Gamma(),
    new League\Glide\Manipulators\Sharpen(),
    new League\Glide\Manipulators\Filter(),
    new League\Glide\Manipulators\Blur(),
    new League\Glide\Manipulators\Pixelate(),
    new League\Glide\Manipulators\Output(),
];

// Set API
$api = new League\Glide\Api($imageManager, $manipulators);

// Setup Glide server
$server = new League\Glide\Server($source, $cache, $api, $signKey);
~~~