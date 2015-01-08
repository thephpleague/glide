---
layout: default
permalink: simple-example/
title: Simple Example
---

# Simple Example

Let's say your creating a user profile page which displays a user's name and their profile photo. The user has already uploaded an image, but it hasn't been resized yet, all you have is the original file saved somewhere. The following example illustrates how easy Glide makes cropping and resizing the profile image, without having to do any image processing ahead of time.

## In your templates

In your templates you'll define how the image will be manipulated. Using Glide's HTTP based API, simply set the image manipulations in the image `src` attribute.

<div class="filename">profile.php</div>
~~~ php
<h1><?=$user->name?></h1>

<!-- Display profile image cropped to 300x400 -->
<img src="/img/users/<?=$user->id?>.jpg?w=300&h=400&fit=crop">
~~~

## In your routes

Next, within your routes, setup a Glide server. Tell it where the source images can be found, and also where the manipulated images it generates (the cache) should be saved. Finally pass the server the request, and it will handle all the image manipulations and will output the image.

<div class="filename">routes.php</div>
~~~ php
use League\Glide\Factories\Server;
use Symfony\Component\HttpFoundation\Request;

// Setup Glide server
$glide = Server::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
    'base_url' => '/img/',
]);

// Output image based on the current URL
$glide->outputImage(Request::createFromGlobals());
~~~

