---
layout: default
title: Setup
---

# Setup

All the Glide configuration is managed through a central object called the `Server`. This includes the image [source location](config/source-and-cache/) (where the original images are saved), the image [cache location](config/source-and-cache/) (where the manipulated images are saved), as well as all other configuration options.

## Setup with factory

The easiest way to configure the `Server` is using the supplied factory.

~~~ php
<?php

$server = League\Glide\ServerFactory::create([
    'source' =>                  // Source filesystem
    'source_path_prefix' =>      // Source filesystem path prefix
    'cache' =>                   // Cache filesystem
    'cache_path_prefix' =>       // Cache filesystem path prefix
    'temp_dir' =>                // Temporary directory where cache EXIF data should be stored 
                                 // (defaults to sys_get_temp_dir())
    'group_cache_in_folders' =>  // Whether to group cached images in folders
    'watermarks' =>              // Watermarks filesystem
    'watermarks_path_prefix' =>  // Watermarks filesystem path prefix
    'driver' =>                  // Image driver (gd or imagick)
    'max_image_size' =>          // Image size limit
    'defaults' =>                // Default image manipulations
    'presets' =>                 // Preset image manipulations
    'base_url' =>                // Base URL of the images
    'response' =>                // Response factory
]);
~~~

## Setup manually

You can also choose to instantiate the `Server` object manually. This allows finer control over what dependencies are being used. For example, if you wanted to add additional functionality to the API, you could load custom manipulators in addition to those provided with Glide.

~~~ php
<?php

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
    'driver' => 'gd',
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

// Setup Glide server
$server = new League\Glide\Server(
    $source,
    $cache,
    $api,
);

// Set response factory
$server->setResponseFactory(new SymfonyResponseFactory());
~~~

<p class="message-notice">Be sure to always include the <code>League\Glide\Manipulators\Encode()</code> manipulator as the last item in your list of manipulators. This manipulator is required, and Glide will break without it.</p>

<p class="message-notice">To use the <code>SymfonyResponseFactory</code> class, you must also include the <code>symfony/http-foundation</code> package with your project. For more information, see <a href="config/responses/">responses</a>.</p>
