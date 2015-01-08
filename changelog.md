---
layout: default
permalink: changelog/
title: Changelog
---

# Changelog

All notable changes to Glide will be documented in this file.

## 0.2.0

- Removed `sign_key` option from the `Server` class. For simplicity HTTP signatures are now configured and managed separately from the server.
- Renamed `SignKey` to `HttpSignature`.
- Renamed `InvalidTokenException` to `InvalidSignatureException`.
- Added new `HttpSignature` interface, allowing for custom implementations.
- Added new `HttpSignature` and `UrlBuilder` factories.
- Added new base URL option to server class.

## 0.1.1

- Added new `cacheFileExists` and `sourceFileExists` methods to server.
- Updated `getCacheFilename` method to accept a `Request` object or `$filename`, `$params` combination.

## 0.1.0

- First release, woohoo!