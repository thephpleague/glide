---
layout: default
title: Advanced usage
---

# Advanced usage

Once your Glide [server](../the-server/) is configured, there are a number of methods available to interact with it. For basic setups you'll likely only need the `outputImage()` method. However, if you plan to use Glide with a queuing server or in other more complex configurations, these methods can be useful.

## Source

~~~ php
public function setSource(FilesystemInterface $source)
public function getSource()
public function setSourcePathPrefix($sourcePathPrefix)
public function getSourcePathPrefix()
public function getSourcePath($path)
public function getSourcePathWithoutPrefix($path)
public function sourceFileExists($path)
~~~

## Cache

~~~ php
public function setCache(FilesystemInterface $cache)
public function getCache()
public function setCachePathPrefix($cachePathPrefix)
public function deleteCache($path)
public function getCachePathPrefix()
public function getCachePath($path, array $params)
public function cacheFileExists($path, array $params)
~~~

## Api

~~~ php
public function setApi(ApiInterface $api)
public function getApi()
~~~

## Responses

~~~ php
public function setResponseFactory(ResponseFactoryInterface $responseFactory)
public function getResponseFactory()
~~~

## Default manipulations

~~~ php
public function setDefaultManipulations($defaultManipulations = [])
public function getDefaultManipulations()
~~~

## Base URL

~~~ php
public function setBaseUrl($baseUrl)
public function getBaseUrl()
~~~

## Image generation

~~~ php
// Generates and outputs the image
$server->outputImage($path, array $params);

// Generates and returns the image reponse
$server->getImageResponse($path, array $params);

// Generates and returns the image Base64 encoded 
$server->getImageAsBase64($path, array $params);

// Generates the image
$server->makeImage($path, array $params);
~~~