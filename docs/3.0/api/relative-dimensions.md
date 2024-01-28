---
layout: default
title: Relative dimensions
---

# Relative dimensions

Relative dimensions allow you to specify a width or height value as a percentage of the main image. This is helpful for features like watermarks and borders.

To use a relative dimension, simply provide a percentage as a number (between `0` and `100`), followed by a `w` (width) or `h` (height). For example, `5w` represents 5% of the width of the main image.

~~~ html
<img src="kayaks.jpg?mark=logo.png&markw=5w">
~~~