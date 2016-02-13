---
layout: default
title: Basic usage
---

# Basic usage

Once your [Glide server](/config/the-server/) is configured, there are a number of methods available to interact with it. For basic setups you'll likely only need the `outputImage()` method. However, if you plan to use Glide with a queuing server or in other more complex configurations, these methods will be useful.

## Available methods

~~~ php
<?php

$server->getSourcePath();       // Get the source path
$server->getCachePath();        // Get the cache path
$server->sourceFileExists();    // Check if a source file exists
$server->cacheFileExists();     // Check if a cache file exists
$server->outputImage();         // Generate and output manipulated image
$server->getImageResponse();    // Generate and return response object of manipulated image
$server->makeImage();           // Generate manipulated image
~~~

## Accepted method parameters

All of the above methods will accept an instance of the `Symfony\Component\HttpFoundation\Request` class. However, sometimes it's desirable to manually pass the image path and manipulation parameters. Glide makes this easy by also allowing you to pass a `$path` and `$params` combination to the above methods. Consider the following example:

~~~ php
<?php

Route::get('img/users/{id}/small', function($id) use ($server) {
    $server->outputImage(
        '/users/' . $id . '.jpg',
        [
            'w' => 300,
            'h' => 400,
            'fit' => 'crop',
        ]
    );
});
~~~