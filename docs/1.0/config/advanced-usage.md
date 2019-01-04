---
layout: default
title: Advanced usage
---

# Advanced usage

Once your Glide [server](/1.0/config/setup/) is configured, there are a number of methods available to interact with it. For basic setups you'll likely only need the `outputImage()` method. However, if you plan to use Glide with a queuing server or in other more complex configurations, these methods can be useful.

## Source

~~~ php
<?php

// Set the source file system
public function setSource(FilesystemInterface $source)

// Get the source file system
public function getSource()

// Set the source path prefix
public function setSourcePathPrefix($sourcePathPrefix)

// Get the source path prefix
public function getSourcePathPrefix()

// Get the source path for an image
public function getSourcePath($path)

// Check if a source file exists
public function sourceFileExists($path)
~~~

## Cache

~~~ php
<?php

// Set the cache file system
public function setCache(FilesystemInterface $cache)

// Get the cache file system
public function getCache()

// Set the cache path prefix
public function setCachePathPrefix($cachePathPrefix)

// Get the cache path prefix
public function getCachePathPrefix()

// Set the group cache in folders setting
public function setGroupCacheInFolders(true|false)

// Get the group cache in folders setting
public function getGroupCacheInFolders()

// Delete an image from the cache
public function deleteCache($path)

// Get the cache path for an image
public function getCachePath($path, array $params)

// Check if a cache file exists
public function cacheFileExists($path, array $params)
~~~

## Api

~~~ php
<?php

// Set the image manipulation Api
public function setApi(ApiInterface $api)

// Get the image manipulation Api
public function getApi()
~~~

## Responses

~~~ php
<?php

// Set the response factory
public function setResponseFactory(ResponseFactoryInterface $responseFactory)

// Get the response factory
public function getResponseFactory()
~~~

## Default manipulations

~~~ php
<?php

// Set the default manipulators
public function setManipulators(array $manipulators)

// Get the default manipulators
public function getManipulators()
~~~

## Base URL

~~~ php
<?php

// Set the base url
public function setBaseUrl($baseUrl)

// Get the base url
public function getBaseUrl()
~~~

## Image generation

~~~ php
<?php

// Generates and outputs the image
$server->outputImage($path, array $params);

// Generates and returns the image reponse
$server->getImageResponse($path, array $params);

// Generates and returns the image Base64 encoded
$server->getImageAsBase64($path, array $params);

// Generates the image
$server->makeImage($path, array $params);
~~~
