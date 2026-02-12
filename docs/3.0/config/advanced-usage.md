---
layout: default
title: Advanced usage
---

# Advanced usage

Once your Glide [server](config/setup/) is configured, there are a number of methods available to interact with it. For basic setups you'll likely only need the `outputImage()` method. However, if you plan to use Glide with a queuing server or in other more complex configurations, these methods can be useful.

## Source

~~~ php
<?php

// Set the source file system
public function setSource(\League\Flysystem\FilesystemOperator $source): void

// Get the source file system
public function getSource(): \League\Flysystem\FilesystemOperator

// Set the source path prefix
public function setSourcePathPrefix(string $sourcePathPrefix): void

// Get the source path prefix
public function getSourcePathPrefix(): string

// Get the source path for an image
public function getSourcePath(string $path): string

// Check if a source file exists
public function sourceFileExists(string $path): bool
~~~

## Cache

~~~ php
<?php

// Set the cache file system
public function setCache(\League\Flysystem\FilesystemOperator $cache): void

// Get the cache file system
public function getCache(): \League\Flysystem\FilesystemOperator

// Set the cache path prefix
public function setCachePathPrefix(string $cachePathPrefix): void

// Get the cache path prefix
public function getCachePathPrefix(): string

// Set the group cache in folders setting
public function setGroupCacheInFolders(bool $groupCacheInFolders): void

// Get the group cache in folders setting
public function getGroupCacheInFolders(): bool

// Delete an image from the cache
public function deleteCache(string $path): bool

// Get the cache path for an image
public function getCachePath(string $path, array $params = []): string

// Check if a cache file exists
public function cacheFileExists(string $path, array $params): bool

// Set the temporary directory that should be used to store EXIF data
public function setTempDir(string $tempDir): void

// Get the current temporary directory
public function getTempDir(): string
~~~

## Api

~~~ php
<?php

// Set the image manipulation Api
public function setApi(\League\Glide\Api\ApiInterface $api): void

// Get the image manipulation Api
public function getApi(): \League\Glide\Api\ApiInterface
~~~

## Responses

~~~ php
<?php

// Set the response factory
public function setResponseFactory(?\League\Glide\Responses\ResponseFactoryInterface $responseFactory = null): void

// Get the response factory
public function getResponseFactory(): ?\League\Glide\Responses\ResponseFactoryInterface
~~~

## Default manipulations

~~~ php
<?php

// Set the default manipulations
public function setDefaults(array $defaults): void

// Get the default manipulations
public function getDefaults(): array

// Set the preset manipulations
public function setPresets(array $presets): void

// Get the preset manipulations
public function getPresets(): array
~~~

## Base URL

~~~ php
<?php

// Set the base url
public function setBaseUrl(string $baseUrl): void

// Get the base url
public function getBaseUrl(): string
~~~

## Image generation

~~~ php
<?php

// Generates and outputs the image
$server->outputImage(string $path, array $params): void

// Generates and returns the image response
$server->getImageResponse(string $path, array $params): mixed

// Generates and returns the image Base64 encoded
$server->getImageAsBase64(string $path, array $params): string

// Generates the image
$server->makeImage(string $path, array $params): string
~~~
