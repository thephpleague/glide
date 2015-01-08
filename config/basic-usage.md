---
layout: default
permalink: config/basic-usage/
title: Basic usage
---

# Basic usage

Once your [Glide server](/config/the-server/) is configured, there are a number of methods available to interact with it. For basic setups you'll likely only need the `outputImage()` method. However, if you plan to use Glide with a queuing server or in other more complex configurations, these methods will be useful.

## Available methods

~~~ php
// Get the source filename
$server->getSourceFilename();

// Get the cache filename
$server->getCacheFilename();

// Check if a source file exists
$server->sourceFileExists();

// Check if a cache file exists
$server->cacheFileExists();

// Generate and output manipulated image
$server->outputImage();

// Generate and return response object of manipulated image
$server->getImageResponse();

// Generate manipulated image
$server->makeImage();
~~~

## Accepted method parameters

All of the above methods will accept an instance of the `Symfony\Component\HttpFoundation\Request` class. However, sometimes it's desirable to manually pass the image filename and manipulation parameters. Glide makes this easy by also allowing you to pass a `$filename` and `$params` combination to the above methods. Consider the following example:

~~~ php
Route::get('img/users/{id}/small', function($id) use ($server)
{
    $server->outputImage(
        '/storage/users/' . $id . '.jpg',
        [
            'w' => 300,
            'h' => 400,
            'fit' => 'crop',
        ]
    );
});
~~~