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

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&mark=billabong.png&markw=30w&markpad=3w&markpos=top-right)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&mark=billabong.png&markw=30w&markpad=3w&markpos=top-right)

### Configuring the watermarks file system

Configuring the watermarks file system is exactly the same as configuring the `source` and `cache` file systems. See the [source & cache](config/source-and-cache/) for more information about setting up file systems.

~~~ php
<?php

$server = ServerFactory::create([
    'watermarks' => new Filesystem(new Local('path/to/watermarks/folder')),
    'watermarks_path_prefix' => 'images/watermarks', // optional
]);
~~~

## Width `markw`

Sets the width of the watermark in pixels, or using [relative dimensions](api/relative-dimensions/).

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200">
~~~

## Height `markh`

Sets the height of the watermark in pixels, or using [relative dimensions](api/relative-dimensions/).

~~~ html
<img src="kayaks.jpg?mark=logo.png&markh=200">
~~~

## Fit `markfit`

Sets how the watermark is fitted to its target dimensions.

### Accepts:

- `contain`: Default. Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio.
- `max`: Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio, and will also not increase the size of the image if it is smaller than the output size.
- `fill`: Resizes the image to fit within the width and height boundaries without cropping or distorting the image, and the remaining space is filled with the background color. The resulting image will match the constraining dimensions.
- `stretch`: Stretches the image to fit the constraining dimensions exactly. The resulting image will fill the dimensions, and will not maintain the aspect ratio of the input image.
- `crop`: Resizes the image to fill the width and height boundaries and crops any excess image data. The resulting image will match the width and height constraints without distorting the image. See the [crop](api/crop/) page for more information.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&markh=200&markfit=crop">
~~~

## X-offset `markx`

Sets how far the watermark is away from the left and right edges of the image. Set in pixels, or using [relative dimensions](api/relative-dimensions/). Ignored if `markpos` is set to `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&markx=20">
~~~

## Y-offset `marky`

Sets how far the watermark is away from the top and bottom edges of the image. Set in pixels, or using [relative dimensions](api/relative-dimensions/). Ignored if `markpos` is set to `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&marky=20">
~~~

## Padding `markpad`

Sets how far the watermark is away from edges of the image. Basically a shortcut for using both `markx` and `marky`. Set in pixels, or using [relative dimensions](api/relative-dimensions/). Ignored if `markpos` is set to `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=200&markpad=20">
~~~

## Position `markpos`

Sets where the watermark is positioned. Accepts `top-left`, `top`, `top-right`, `left`, `center`, `right`, `bottom-left`, `bottom`, `bottom-right`. Default is `center`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markpos=top-left">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&mark=billabong.png&markw=30w&markpad=3w&markpos=top-left)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&mark=billabong.png&markw=30w&markpad=3w&markpos=top-left)

## Alpha `markalpha`

Sets the opacity of the watermark. Use values between `0` and `100`, where `100` is fully opaque, and `0` is fully transparent. The default value is `100`.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markalpha=50">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&mark=billabong.png&markw=94w&markpad=3w&markpos=top-right&markalpha=50)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&mark=billabong.png&markw=94w&markpad=3w&markpos=top-right&markalpha=50)