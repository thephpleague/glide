---
layout: default
title: Base URL
---

# Base URL

It's common to route all images under the path `/img/`. However, since Glide maps the image request path directly to the image source path, you would need to have an `/img/` folder in your source location as well. For example:

~~~ php
'http://example.com/img/kayaks.jpg' => '/path/to/source/img/kayaks.jpg'
~~~

The `base_url` allows you to define which part of the URL should be omitted from the source path.

## Set the base URL

~~~ php
// Set using factory
$server = League\Glide\ServerFactory::create([
    'base_url' => '/img/',
]);

// Set using setter method
$server->setBaseUrl('/img/');
~~~

With the base URL configured, the new image source paths will no longer include `/img/`. 

~~~ php
'http://example.com/img/kayaks.jpg' => '/path/to/source/kayaks.jpg'
~~~