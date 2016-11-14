---
layout: default
title: Base URL
---

# Base URL

It's common to route all images under the path `/img/`. However, since Glide maps image request paths directly to the image source paths, you would have to have an `/img/` folder in your source location as well. For example:

~~~ js
'http://example.com/img/kayaks.jpg' => '/path/to/source/img/kayaks.jpg'
'http://example.com/img/users/jonathan.jpg' => '/path/to/source/img/users/jonathan.jpg'
~~~

Since this isn't ideal, Glide allows you to define a `base_url` which is omitted from the source path.

## Set the base URL

~~~ php
<?php

// Set using factory
$server = League\Glide\ServerFactory::create([
    'base_url' => '/img/',
]);

// Set using setter method
$server->setBaseUrl('/img/');
~~~

With the base URL configured, the new image source paths will no longer include `/img/`.

~~~ js
'http://example.com/img/kayaks.jpg' => '/path/to/source/kayaks.jpg'
'http://example.com/img/users/jonathan.jpg' => '/path/to/source/users/jonathan.jpg'
~~~