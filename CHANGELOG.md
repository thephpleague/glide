# Changelog

All notable changes to Glide will be documented in this file.

## 0.3.1

- Fixed bug with URLs that contain spaces or other special characters. Thanks [@jasonvarga](https://github.com/jasonvarga)! [#33](https://github.com/thephpleague/glide/issues/33])

## 0.3.0

- Large refactor for improve code organization. Many classes moved and renamed.
- Changed `league/flysystem` dependency to version `1.x`. Nice work [@frankdejonge](https://github.com/frankdejonge)!
- Responses will now return `304 Not Modified` on subsequent requests. [#20](https://github.com/thephpleague/glide/issues/20])
- Added new source path prefix option to `Server`, with `setSourcePathPrefix()` and `getSourcePathPrefix()` methods. [#26](https://github.com/thephpleague/glide/issues/26])
- Added new cache path prefix option to `Server`, with `setCachePathPrefix()` and `getCachePathPrefix()` methods. [#26](https://github.com/thephpleague/glide/issues/26])
- Added new `source_path_prefix` and `cache_path_prefix` options to `ServerFactory`. [#26](https://github.com/thephpleague/glide/issues/26])
- Added new `FilesystemException\FilesystemException` exception, and additional file system checks.
- Changed manipulators to return an instance of `Intervention\Image\Image`. This allows for more rigorous manipulations to occur. [#25](https://github.com/thephpleague/glide/issues/25])
- Updated output manipulator to use source format when format is not set or invalid. Previously this was set to `jpg` by default. [#24](https://github.com/thephpleague/glide/issues/24])
- Renamed server method `getSourceFilename()` to `getSourcePath()`, and `getCacheFilename()` to `getCachePath()`. 
- Removed `$baseUrl` parameter from the `Server` constructor. Use `setBaseUrl()` method instead.

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