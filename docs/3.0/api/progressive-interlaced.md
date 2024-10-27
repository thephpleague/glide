---
layout: default
title: Progressive & Interlaced
---

## Progressive & Interlaced Images

## Interlace `interlace`

The `interlace` parameter controls whether an image is rendered in a progressive or interlaced format. This feature enhances the loading experience of images, making them appear gradually as they are downloaded, which can improve the user experience on slower connections.

> Caution: For GIF/PNG, it can generate a slightly larger file size.

### Supported Formats

- **JPG**: The `onterlace` parameter applies a progressive scan to JPG images.
- **PNG** and **GIF**: The `interlace` parameter enables interlacing for GIF/PNG images.

> Note: When `ext` is set to `.pjpg`, it will automatically generate a progressive JPG image, regardless of the `interlace` parameter.

~~~ html
<img src="kayaks.jpg?interlace=1">
<img src="logo.png?interlace=1">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/1.0/kayaks.jpg?interlace=1)](https://glide.herokuapp.com/1.0/kayaks.jpg?interlace=1)
