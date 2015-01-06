---
layout: default
permalink: config/source-and-cache/
title: Source & Cache
---

# Source & Cache

Glide makes it possible to access images stored in a variety of file systems. It does this using the [Flysystem](http://flysystem.thephpleague.com/) file system abstraction library. For example, you may choose to store your source images on [Amazon S3](http://aws.amazon.com/s3/), but keep your rendered images (the cache) on a local disk.

To set your source and cache locations, simply pass an instance of `League\Flysystem\Filesystem` for each. Alternatively, if you are only using the local disk, you can simply pass a path as a string.

~~~ php
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Glide\Factories\Server;

// Setup Glide server
$glide = Server::create([
    'source' => new Filesystem(new Local('source-folder')),
    'cache' => new Filesystem(new Local('cache-folder')),
]);

// Pass strings when using local disk only
$glide = Server::create([
    'source' => 'source-folder',
    'cache' => 'cache-folder',
]);
~~~