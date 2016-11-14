---
layout: default
title: Size
---

# Size

## Width `w`

Sets the width of the image, in pixels.

~~~ html
<img src="kayaks.jpg?w=500">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/0.3/kayaks.jpg?w=500)](https://glide.herokuapp.com/0.3/kayaks.jpg?w=500)

## Height `h`

Sets the height of the image, in pixels.

~~~ html
<img src="kayaks.jpg?h=333">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/0.3/kayaks.jpg?h=333)](https://glide.herokuapp.com/0.3/kayaks.jpg?h=333)

## Fit `fit`

Sets how the image is fitted to its target dimensions.

### Accepts: 

- `contain`: Default. Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio.
- `max`: Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio, and will also not increase the size of the image if it is smaller than the output size. 
- `stretch`: Stretches the image to fit the constraining dimensions exactly. The resulting image will fill the dimensions, and will not maintain the aspect ratio of the input image.
- `crop`: Resizes the image to fill the width and height boundaries and crops any excess image data. The resulting image will match the width and height constraints without distorting the image. See the `crop` parameter for controlling how the image is cropped.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=crop">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/0.3/kayaks.jpg?w=300&h=300&fit=crop)](https://glide.herokuapp.com/0.3/kayaks.jpg?w=300&h=300&fit=crop)

## Crop Mode `crop`

Sets where the image is cropped when the `fit` parameter is set to `crop`. Accepts `top-left`, `top`, `top-right`, `left`, `center`, `right`, `bottom-left`, `bottom` or `bottom-right`. Default is `center`.

~~~ html
<img src="kayaks.jpg?w=300&h=300&fit=crop&crop=left">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/0.3/kayaks.jpg?w=300&h=300&fit=crop&crop=left)](https://glide.herokuapp.com/0.3/kayaks.jpg?w=300&h=300&fit=crop&crop=left)

## Rectangle `rect`

Crops the image to specific dimensions prior to any other resize operations. Required format: `width,height,x,y`.

~~~ html
<img src="kayaks.jpg?rect=100,100,915,155">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/0.3/kayaks.jpg?rect=100,100,915,155)](https://glide.herokuapp.com/0.3/kayaks.jpg?rect=100,100,915,155)


## Orientation `or`

Rotates the image. Accepts `auto`, `0`, `90`, `180` or `270`. Default is `auto`. The `auto` option uses Exif data to automatically orient images correctly.

~~~ html
<img src="kayaks.jpg?h=500&or=90">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/0.3/kayaks.jpg?h=500&or=90)](https://glide.herokuapp.com/0.3/kayaks.jpg?h=500&or=90)