---
layout: default
title: Effects
---

# Effects

## Blur `blur`

Adds a blur effect to the image. Use values between `0` and `100`.

~~~ html
<img src="kayaks.jpg?w=500&blur=5>
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&blur=5)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&blur=5)

<p class="message-notice">Performance intensive on larger amounts of blur with GD driver. Use with care.</p>

## Pixelate `pixel`

Applies a pixelation effect to the image. Use values between `0` and `1000`.

~~~ html
<img src="kayaks.jpg?w=500&pixel=5>
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&pixel=5)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&pixel=5)

## Filter `filt`

Applies a filter effect to the image. Accepts `greyscale` or `sepia`.

~~~ html
<img src="kayaks.jpg?w=500&filt=sepia>
~~~

[![© Photo Joel Reynolds](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&filt=sepia)](https://glide.herokuapp.com/2.0/kayaks.jpg?w=500&filt=sepia)