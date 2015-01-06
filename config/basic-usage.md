---
layout: default
permalink: config/basic-usage/
title: Basic Usage
---

# Basic Usage

Once your [Glide server](/config/the-server/) is configured, there are a number of methods available to interact with it. For basic setups you'll likely only need the `outputImage()` method. However, if you plan to use Glide with a queuing server or in other more complex configurations, these methods will be useful.

## Available methods

~~~ php
// Check if a source file exists
$server->sourceFileExists($request);

// Check if a cache file exists
$server->cacheFileExists($request);

// Get a cache filename
$server->getCacheFilename($request);

// Generate and output manipulated image
$server->outputImage($request);

// Generate and return response object of manipulated image
$server->getImageResponse($request);

// Generate manipulated image
$server->makeImage($request);
~~~

## Accepted method parameters

All of the above methods will accept an instance of the `Symfony\Component\HttpFoundation\Request` class. However, sometimes it is desirable to manually pass the image filename and manipulation parameters. Glide makes this easy by also allowing you to pass a `$filename` and `$params` combination to the above methods. Consider the following example:

~~~ php
Route::get('img/users/{id}/small', function($id)
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