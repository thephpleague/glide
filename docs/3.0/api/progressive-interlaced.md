---
layout: default
title: Progressive & Interlaced
---

## Progressive & Interlaced Images


## Interlace `interlace`

The `interlaced` parameter controls whether an image is rendered in a progressive or interlaced format. This feature enhances the loading experience of images, making them appear gradually as they are downloaded, which can improve the user experience on slower connections.

> Caution: for GIF/PNG, it can generate a sligher larger file size.

### Supported Formats

- **JPG**: The `Interlaced` parameter applies a progressive scan to JPG images.
- **PNG** and **GIF**: The `Interlaced` parameter enables interlacing for GIF/PNG images.

> Note: Case `ext` is set to `.pjpg`, it will automatically generate a progressive JPG image, regardless of the `interlaced` parameter.

~~~ html
<img src="kayaks.jpg?interlaced=1">
<img src="logo.png?interlaced=1">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/1.0/kayaks.jpg?interlace=1)](https://glide.herokuapp.com/1.0/kayaks.jpg?h=500&flip=v)
