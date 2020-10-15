---
layout: default
title: Secure images
---

# Secure images

Add additional security to your Glide image server with HTTP signatures. By signing each request with a private key, no alterations can be made to the URL parameters.

<p class="message-notice">It is highly recommended that you use secure URLs in production environments.</p>

## Configuration

Start by configuring the Glide server to validate each request before you output the image. In the event that the validation fails, Glide will throw an `SignatureException` exception.

~~~ php
<?php

use League\Glide\Http\SignatureFactory;
use League\Glide\Http\SignatureException;
use Symfony\Component\HttpFoundation\Request;

// Create request object
$request = Request::createFromGlobals();

// Validate HTTP signature
try {
    SignatureFactory::create('your-sign-key')->validateRequest($request);
} catch (SignatureException $e) {
    // Handle error
}
~~~

## Generating secure URLs

Next, generate a signature for each image request you make. Glide comes with a URL builder to make this process easy. Be sure to use the same signing key you configured earlier.

~~~ php
<?php

use League\Glide\Http\UrlBuilderFactory;

// Create an instance of the URL builder
$urlBuilder = UrlBuilderFactory::create('http://example.com', 'your-sign-key');

// Generate a URL
$url = $urlBuilder->getUrl('cat.jpg', ['w' => 500]);

// Use the URL in your app
echo '<img src="'.$url.'">';

// Prints out
<img src="http://example.com/img/cat.jpg?w=500&token=af3dc18fc6bfb2afb521e587c348b904">
~~~