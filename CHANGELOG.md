# Changelog

All notable changes to Glide will be documented in this file.

## 0.3.0

- Large refactor, many classes moved and renamed.
- Changed `league/flysystem` dependency to version `1.x`.
- Added new `FilesystemException` exception, and additional file system checks.
- Added new source path prefix option to `Server`, with `setSourcePathPrefix()` and `getSourcePathPrefix()` methods.
- Added new cache path prefix option to `Server`, with `setCachePathPrefix()` and `getCachePathPrefix()` methods.
- Added new `source_path_prefix` and `cache_path_prefix` options to `Factories\Server`.
- Changed manipulators to return an instance of `Intervention\Image\Image`.
- Updated output manipulator to use source format when format is not set or is invalid.
- Renamed server method `getSourceFilename()` to `getSourcePath()`.
- Renamed server method `getCacheFilename()` to `getCachePath()`.
- Removed `$baseUrl` parameter from the `Server` constructor.

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