---
layout: default
title: Source & cache
---

# Source & cache

Glide makes it possible to access images stored in a variety of file systems. It does this using the [Flysystem](http://flysystem.thephpleague.com/) file system abstraction library. For example, you may choose to store your source images on [Amazon S3](http://aws.amazon.com/s3/), but keep your rendered images (the cache) on the local disk.

## Setup using Flysystem

To set your source and cache locations, simply pass an instance of `League\Flysystem\Filesystem` for each. See the Flysystem [website](http://flysystem.thephpleague.com/) for a complete list of available adapters.

~~~ php
<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Glide\ServerFactory;

// Setup Glide server
$server = ServerFactory::create([
    'source' => new Filesystem(new Local('path/to/source/folder')),
    'cache' => new Filesystem(new Local('path/to/cache/folder')),
]);
~~~

## Setup using local disk

Alternatively, if you are only using the local disk, you can simply provide the paths as a string.

~~~ php
<?php

$server = League\Glide\ServerFactory::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
]);
~~~

## Set default path prefix

While it's normally possible to set the full source and cache path using Flysystem, there are situations where it may be desirable to set a default path prefix. For example, when only one instance of the `Filesystem` is available.

~~~ php
<?php

// Set using factory
$server = League\Glide\ServerFactory::create([
    'source' => $filesystem,
    'cache' => $filesystem,
    'source_path_prefix' => 'source',
    'cache_path_prefix' => 'cache',
]);

// Set using setter methods
$server->setSourcePathPrefix('source');
$server->setCachePathPrefix('cache');
~~~