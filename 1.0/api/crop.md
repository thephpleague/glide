---
layout: default
title: Crop
---

# Crop

## Fit `fit=crop`

Resizes the image to fill the width and height boundaries and crops any excess image data. The resulting image will match the width and height constraints without distorting the image.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=crop">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/kayaks.jpg?w=300&h=300&fit=crop)](https://glide.herokuapp.com/kayaks.jpg?w=300&h=300&fit=crop)

### Crop Position

You can also set where the image is cropped by adding a crop position. Accepts `crop-top-left`, `crop-top`, `crop-top-right`, `crop-left`, `crop-center`, `crop-right`, `crop-bottom-left`, `crop-bottom` or `crop-bottom-right`. Default is `crop-center`, and is the same as `crop`.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=crop-left">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/kayaks.jpg?w=300&h=300&fit=crop-left)](https://glide.herokuapp.com/kayaks.jpg?w=300&h=300&fit=crop-left)

## Crop `crop`

Crops the image to specific dimensions prior to any other resize operations. Required format: `width,height,x,y`.

~~~ html
<img src="kayaks.jpg?crop=100,100,915,155">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/kayaks.jpg?crop=100,100,915,155)](https://glide.herokuapp.com/kayaks.jpg?crop=100,100,915,155)