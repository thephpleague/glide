---
layout: default
title: The server
---

# The server

All the Glide configuration is managed through a central object called the `Server`. This includes the image [source location](config/source-and-cache/) (where the original images are saved), the image [cache location](config/source-and-cache/) (where the manipulated images are saved), the image manipulation API as well as any configuration options.

## Setup with factory

The easiest way to configure the `Server` is using the supplied factory.

~~~ php
use League\Glide\ServerFactory;
use League\Glide\Responses\PsrResponseFactory;

// Setup Glide server
$server = ServerFactory::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
    'response' => new SymfonyResponseFactory(),
]);
~~~

<p class="message-notice">To use the <code>SymfonyResponseFactory</code> class, you must also include the <code>league/glide-symfony</code> package. For more information, see <a href="config/responses/">responses</a>.</p>

## Setup manually

You can also choose to instantiate the `Server` object manually. This allows finer control over what dependencies are being used. For example, if you wanted to add additional functionality to the API, you could load custom manipulators in addition to those provided with Glide.

~~~ php
// Set source filesystem
$source = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('path/to/source/folder')
);

// Set cache filesystem
$cache = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('path/to/cache/folder')
);

// Set watermarks filesystem
$watermarks = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('path/to/watermarks/folder')
);

// Set image manager
$imageManager = new Intervention\Image\ImageManager([
    'driver' => 'imagick',
]);

// Set manipulators
$manipulators = [
    new League\Glide\Manipulators\Orientation(),
    new League\Glide\Manipulators\Crop(),
    new League\Glide\Manipulators\Size(2000*2000),
    new League\Glide\Manipulators\Brightness(),
    new League\Glide\Manipulators\Contrast(),
    new League\Glide\Manipulators\Gamma(),
    new League\Glide\Manipulators\Sharpen(),
    new League\Glide\Manipulators\Filter(),
    new League\Glide\Manipulators\Blur(),
    new League\Glide\Manipulators\Pixelate(),
    new League\Glide\Manipulators\Watermark($watermarks),
    new League\Glide\Manipulators\Background(),
    new League\Glide\Manipulators\Border(),
    new League\Glide\Manipulators\Encode(),
];

// Set API
$api = new League\Glide\Api\Api($imageManager, $manipulators);

// Set response factory
$responseFactory = new SymfonyResponseFactory(); // requires league/glide-symfony
$responseFactory = new PsrResponseFactory($response); // requires PSR-7 compliant library

// Setup Glide server
$server = new League\Glide\Server(
    $source,
    $cache,
    $api,
    $responseFactory
);
~~~