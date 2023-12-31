---
layout: default
title: Trim
---

# Trim

## Trim `trim=top-left`

Trims away image space in color at given position.

~~~ html
<img src="kayaks.jpg?h=500&trim=top-left">
~~~

### Trim Base

Sets the the point from where the trimming color is picked.

### Accepts: 

- `top-left`: Default.
- `buttom-right`
- `transparent`

~~~ html
<img src="kayaks.jpg?h=500&trim=bottom-right">
~~~

### Trim Away

Sets which borders should be trimmed away. Defined by the first letter of each side; `t`, `b`, `l`, `r` (top, bottom, left, light). Default is all sides.

~~~ html
<img src="kayaks.jpg?h=500&trim=bottom-right,tl">
~~~

### Trim Tolerance

Sets tolerance level in percent to trim similar colors. Default is `0`.

~~~ html
<img src="kayaks.jpg?h=500&trim=bottom-right,tl,5">
~~~

### Trim Feather

Sets the border around the object, in pixels. Can be a positive value, to expand the border, or negative, to contract the border. Default is `0`.

~~~ html
<img src="kayaks.jpg?h=500&trim=bottom-right,tl,5,10">
~~~