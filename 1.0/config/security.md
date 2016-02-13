---
layout: default
title: Security
---

# Security

Secure your Glide image server with HTTP signatures. By signing each request with a private key, no alterations can be made to the URL parameters.

<p class="message-notice">It is highly recommended that you use signed URLs in production environments, otherwise your application will be open to mass image-resize attacks.</p>

## Configuration

Start by configuring the Glide server to validate each request before you ouput the image. In the event that the validation fails, Glide will throw an `SignatureException` exception.

~~~ php
<?php

use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureException;

try {
    // Set complicated sign key
    $signkey = 'v-LK4WCdhcfcc%jt*VC2cj%nVpu+xQKvLUA%H86kRVk_4bgG8&CWM#k*b_7MUJpmTc=4GFmKFp7=K%67je-skxC5vz+r#xT?62tT?Aw%FtQ4Y3gvnwHTwqhxUh89wCa_';

    // Validate HTTP signature
    SignatureFactory::create($signkey)->validateRequest($path, $_GET);

} catch (SignatureException $e) {
    // Handle error
}
~~~

<p class="message-notice">We recommend using a 128 character (or larger) signing key to prevent trivial key attacks. Consider using a package like <a href="https://github.com/AndrewCarterUK/CryptoKey">CryptoKey</a> to generate a secure key.</p>

## Generating secure URLs

Next, generate a signature for each image request you make. Glide comes with a URL builder to make this process easy. Be sure to use the same signing key you configured earlier.

~~~ php
<?php

use League\Glide\Urls\UrlBuilderFactory;

// Set complicated sign key
$signkey = 'v-LK4WCdhcfcc%jt*VC2cj%nVpu+xQKvLUA%H86kRVk_4bgG8&CWM#k*b_7MUJpmTc=4GFmKFp7=K%67je-skxC5vz+r#xT?62tT?Aw%FtQ4Y3gvnwHTwqhxUh89wCa_';

// Create an instance of the URL builder
$urlBuilder = UrlBuilderFactory::create('/img/', $signkey);

// Generate a URL
$url = $urlBuilder->getUrl('cat.jpg', ['w' => 500]);

// Use the URL in your app
echo '<img src="'.$url.'">';

// Prints out
<img src="/img/cat.jpg?w=500&s=af3dc18fc6bfb2afb521e587c348b904">
~~~

## Max image size

In addition signing URLs, you can also limit how large images can be generated. The following setting will set the maximum allowed total image size, in pixels.

~~~ php
<?php

$server = League\Glide\ServerFactory::create([
    'max_image_size' => 2000*2000,
]);
~~~

Notice that Glide doesn't actually restrict the width or height, but rather the total image size. In the above example it would be `4000000px`. This accomplishes the same thing, while offering more flexibility with your image sizes.