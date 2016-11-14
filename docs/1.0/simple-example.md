---
layout: default
title: Simple example
---

# Simple example

Say you're creating a user profile page that displays a user's name and profile photo. The user has already uploaded an image, but it hasn't been resized. All you have is the original file saved somewhere. The following example illustrates how easy Glide makes cropping and resizing the profile image without having to do any image processing ahead of time.

## In your templates

In your templates simply define how the image will be manipulated. Using Glide's HTTP based API, set the image manipulations in the profile image's `src` attribute.

<div class="filename">profile.php</div>
~~~ php
<h1><?=$user->name?></h1>

<!-- display profile image cropped to 300x400 -->
<img src="/img/users/<?=$user->id?>.jpg?w=300&h=400&fit=crop">
~~~

<p class="message-notice">For simplicity this example has omitted HTTP signatures, however in a production environment it's very important to <a href="/1.0/config/security/">secure your images</a>.</p>

## In your routes

Next, within your routes, setup your Glide server. Configure where the source images can be found, as well as where the manipulated images should be saved (the cache). Finally pass the server the request. It will handle all the image manipulations and will output the image.

<div class="filename">routes.php</div>
~~~ php
<?php

// Setup Glide server
$server = League\Glide\ServerFactory::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
]);

// You could manually pass in the image path and manipulations options
$server->outputImage('users/1.jpg', ['w' => 300, 'h' => 400]);

// But, a better approach is to use information from the request
$server->outputImage($path, $_GET);
~~~

<p class="message-notice">While the <code>outputImage()</code> method works okay, the <code>getImageResponse()</code> method is recommended. This allows your application to handle the outputting of the manipulated images. This approach does requires a little more configuration. See <a href="config/responses/">responses</a> for more info.</p>
