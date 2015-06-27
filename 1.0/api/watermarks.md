---
layout: default
title: Watermarks
---

# Watermarks

## Path `mark`

Adds a watermark to the image. Must be a path to an image in the watermarks file system, as configured in your server.

~~~ html
<img src="kayaks.jpg?mark=logo.png">
~~~

### Configuring the watermarks file system

Configuring the watermarks file system is exactly the same as configuring the `source` and `cache` file systems. See the [source & cache](../../config/source-and-cache/) for more information about setting up file systems.

~~~ php
$server = ServerFactory::create([
    'watermarks' => new Filesystem(new Local('path/to/watermarks/folder')),
    'watermarks_path_prefix' => 'images/watermarks', // optional
]);
~~~

## Width `markw`

Sets the width of the watermark in pixels, or using [relative dimensions](../relative-dimensions/).

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200">
~~~

## Height `markh`

Sets the height of the watermark in pixels, or using [relative dimensions](../relative-dimensions/).

~~~ html
<img src="kayaks.jpg?mark=logo.png&markh=200">
~~~

## X-offset `markx`

Sets how far the watermark is away from the left and right edges of the image. Set in pixels, or using [relative dimensions](../relative-dimensions/). Ignored if `markpos` is set to `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&markx=20">
~~~

## Y-offset `marky`

Sets how far the watermark is away from the top and bottom edges of the image. Set in pixels, or using [relative dimensions](../relative-dimensions/). Ignored if `markpos` is set to `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&marky=20">
~~~

## Padding `markpad`

Sets how far the watermark is away from edges of the image. Basically a shortcut for using both `markx` and `marky`. Set in pixels, or using [relative dimensions](../relative-dimensions/). Ignored if `markpos` is set to `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&markpad=20">
~~~

## Position `markpos`

Sets where the watermark is positioned. Accepts `top-left`, `top`, `top-right`, `left`, `center`, `right`, `bottom-left`, `bottom`, `bottom-right`. Default is `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&markpad=20">
~~~