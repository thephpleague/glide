---
layout: default
permalink: config/source-and-cache/
title: Source & cache
---

# Source & cache

Glide makes it possible to access images stored in a variety of file systems. It does this using the [Flysystem](http://flysystem.thephpleague.com/) file system abstraction library. For example, you may choose to store your source images on [Amazon S3](http://aws.amazon.com/s3/), but keep your rendered images (the cache) on the local disk.

## Setup using Flysystem

To set your source and cache locations, simply pass an instance of `League\Flysystem\Filesystem` for each. See the [Flysystem](http://flysystem.thephpleague.com/) website for a complete list of available adapters.

~~~ php
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Glide\Factories\Server;

// Setup Glide server
$glide = Server::create([
    'source' => new Filesystem(new Local('path/to/source/folder')),
    'cache' => new Filesystem(new Local('path/to/cache/folder')),
]);
~~~

## Setup using local disk

Alternatively, if you are only using the local disk, you can simply provide the paths as a string.

~~~ php
use League\Glide\Factories\Server;

// Setup Glide server
$glide = Server::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
]);
~~~