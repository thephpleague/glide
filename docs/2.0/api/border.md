---
layout: default
title: Border
---

# Border

## Border `border`

Add a border to the image. Required format: `width,color,method`.

~~~ html
<img src="kayaks.jpg?w=500&border=10,5000,overlay">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&border=10,5000,overlay)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&border=10,5000,overlay)

### Width

Sets the border width in pixels, or using [relative dimensions](api/relative-dimensions/).

### Color

Sets the border color. See [colors](api/colors/) for more information on the available color formats.

### Method

Sets how the border will be displayed. Available options:

- `overlay`: Place border on top of image (default).
- `shrink`: Shrink image within border (canvas does not change).
- `expand`: Expands canvas to accommodate border.

~~~ html
<img src="kayaks.jpg?w=500&border=10,FFCC33,expand">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&border=10,FFCC33,expand)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&border=10,FFCC33,expand)