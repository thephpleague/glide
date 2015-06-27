---
layout: default
title: Border
---

# Border

## Border `border`

Add a border to the image. Required format: `width,color,method`.

~~~ html
<img src="kayaks.jpg?w=500&border=15,red,overlay">
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/kayaks.jpg?w=500&border=15,red,overlay)](https://glide.herokuapp.com/kayaks.jpg?w=500&border=15,red,overlay)

### Width

Sets the border width in pixels, or using [relative dimensions](../relative-dimensions/).

### Color

Sets the border color. See [colors](../colors/) for more information on the available color formats.

### Method

Sets how the border will be displayed. Available options:

- `overlay`: Place border on top of image (default).
- `shrink`: Shrink image within border (canvas does not change).
- `expand`: Expands canvas to accommodate border.

