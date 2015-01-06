---
layout: default
permalink: config/secure-images/
title: Secure Images
---

# Secure Images

Add additional security to your Glide image server with a signing key. This requires the passing of a token with each request and prevents any altering of the URL parameters.

<p class="message-notice">It is highly recommended that you use secure URLs in production environments.</p>

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

Next, generate a secure token whenever you request an image from your server. For example, instead of requesting `image.jpg?w=1000`, you would instead request `image.jpg?w=1000&token=af3dc18fc6bfb2afb521e587c348b904`. Glide comes with a URL builder to make this process easy.

~~~ php
use League\Glide\UrlBuilder;

// Create an instance of the URL builder
$urlBuilder = new UrlBuilder('http://example.com', 'your-sign-key');

// Generate a url
$url = $urlBuilder->getUrl('image.jpg', ['w' => 1000]);

// Use the url in your app
echo '<img src="'.$url.'">';

// Prints out
// <img src="http://example.com/image.jpg?w=1000&token=af3dc18fc6bfb2afb521e587c348b904">
~~~