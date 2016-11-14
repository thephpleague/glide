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

## Set a base URL

It's common to route all images under the path `/img/`. However, since Glide maps the image request path directly to the image source path, you would need to have an `/img/` folder in your source location as well. For example:

~~~ js
'http://example.com/img/kayaks.jpg' => '/path/to/source/img/kayaks.jpg'
~~~

The `base_url` allows you to define which part of the URL should be omitted from the source path.

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
~~~

## Disabling the cache

In some situations it may be desirable to disable the cache. For example, you may choose to use a tool like Varnish for caching instead. The best way to do this with Glide is to use an [in-memory adapter](http://flysystem.thephpleague.com/adapter/memory/) for Flysystem. This will prevent any cached images from being saved to your local disk.

## Grouping cache in folders

By default Glide groups cached images into folders. For example, all variations of `kayaks.jpg` will be found in a `/path/to/cache/folder/kayaks.jpg` folder. If you'd prefer to have all cached images in the same folder, this can be done in two ways:

~~~ php
<?php

// Set using factory
$server = League\Glide\ServerFactory::create([
    'group_cache_in_folders' => false
]);

// Set using setter method
$server->setGroupCacheInFolders(false);
~~~

## Deleting cached images

Glide does not automatically purge cached images. However, this can be done by your application using the `deleteCache()` method. Note, grouping cache in folders MUST be enabled in order to delete cached images, which is the default setting.

~~~ php
<?php

$server->deleteCache('kayaks.jpg');
~~~