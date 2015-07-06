---
layout: default
title: Simple example
---

# Simple example

Let's say you're creating a user profile page that displays a user's name and their profile photo. The user has already uploaded an image, but it hasn't been resized yet, all you have is the original file saved somewhere. The following example illustrates how easy Glide makes cropping and resizing the profile image, without having to do any image processing ahead of time.

## In your templates

In your templates simply define how the image will be manipulated. Following Glide's HTTP based API, set the image manipulations in the profile image's `src` attribute.

<div class="filename">profile.php</div>
~~~ php
<h1><?=$user->name?></h1>

<!-- display profile image cropped to 300x400 -->
<img src="/img/users/<?=$user->id?>.jpg?w=300&h=400&fit=crop">
~~~

## In your routes

Next, within your routes, setup a Glide server. Configure where the source images can be found as well as where the manipulated images should be saved (the cache). Finally pass the server the request. It will handle all the image manipulations and will output the image.

<div class="filename">routes.php</div>
~~~ php
// Setup Glide server
$server = League\Glide\ServerFactory::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
]);

// You could manually output the image, just
// pass a path as well as the manipulation options
$server->outputImage('users/1.jpg', ['w' => 300, 'h' => 400]);

// Or better yet, output the image based on the current URL
$server->outputImage($path, $_GET);
~~~

<p class="message-notice">While the <code>outputImage()</code> method works great when you're first getting started, it's recommended that you actually use the <code>getImageResponse()</code> method instead. This allows your application to handle the outputting of the manipulated images. For more information, see <a href="config/responses/">responses</a>.</p>