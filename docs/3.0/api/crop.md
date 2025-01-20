---
layout: default
title: Crop
---

# Crop

## Fit `fit=cover` - Crop to cover the dimensions

Resizes the image to fill the width and height boundaries and crops any excess image data. The resulting image will match the width and height constraints without distorting the image.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=cover">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/1.0/kayaks.jpg?w=300&h=300&fit=crop)](https://glide.herokuapp.com/1.0/kayaks.jpg?w=300&h=300&fit=crop)

### Crop Position

You can also set where the image is cropped by adding a crop position. Accepts `cover-top-left`, `cover-top`, `cover-top-right`, `cover-left`, `cover-center`, `cover-right`, `cover-bottom-left`, `cover-bottom` or `cover-bottom-right`. Default is `cover-center`, and is the same as `crop`.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=cover-left">
~~~

## Fit `fit=crop-x%-y%` - Crop based on Focal Point

In addition to the crop position, you can be more specific about the exact crop position using a focal point. This is defined using two offset percentages: `crop-x%-y%`.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=crop-25-75">
~~~

You may also choose to zoom into your focal point by providing a third value: a float between 1 and 100. Each full step is the equivalent of a 100% zoom. (eg. `x%-y%-2` is the equivalent of viewing the image at 200%). The suggested range is 1-10.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=crop-25-75-2">
~~~

## Crop `crop`

Crops the image to specific dimensions prior to any other resize operations. Required format: `width,height,x,y`.

~~~ html
<img src="kayaks.jpg?crop=100,100,915,155">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/1.0/kayaks.jpg?crop=100,100,915,155)](https://glide.herokuapp.com/1.0/kayaks.jpg?crop=100,100,915,155)
