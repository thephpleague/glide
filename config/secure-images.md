---
layout: default
permalink: config/secure-images/
title: Secure Images
---

# Secure Images

If you want additional security on your images, you can add a secure signature so that no one can alter the image parameters. This is highly recommended for production environments.

## Configuration

Start by setting a signing key in the Glide server configuration:

~~~ php
use League\Glide\Factories\Server;

// Add signing key in Glide configuration
$glide = Server::create([
    'sign_key' => 'your-sign-key',
]);
~~~

## Generating secure URLs

Next, generate a secure token whenever you request an image from your server. For example, instead of requesting `image.jpg?w=1000`, you would instead request `image.jpg?w=1000&token=6db10b02a4132a8714b6485d1138fc87`. Glide comes with a URL builder to make this process easy.

~~~ php
use League\Glide\UrlBuilder;

// Create an instance of the URL builder
$urlBuilder = new UrlBuilder('http://your-website.com', 'your-sign-key');

// Generate a url
$url = $urlBuilder->getUrl('image.jpg', ['w' => 1000]);

// Use the url in your app
echo '<img src="'.$url.'">';

// Prints out
// <img src="http://your-website.com/image.jpg?w=1000&token=af3dc18fc6bfb2afb521e587c348b904">
~~~