---
layout: default
title: Pixel Density
---

# Pixel Density

## Device pixel ratio `dpr`

The device pixel ratio is used to easily convert between CSS pixels and device pixels. This makes it possible to display images at the correct pixel density on a variety of devices such as Apple devices with Retina Displays and Android devices. You must specify either a width, a height, or both for this parameter to work. The default is 1. The maximum value that can be set for dpr is 8.

~~~ html
<img src="kayaks.jpg?w=250&dpr=2">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=250&dpr=2)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=250&dpr=2)