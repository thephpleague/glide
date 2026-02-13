---
layout: default
title: Encode
---

# Encode

## Quality `q`

Defines the quality of the image. Use values between `0` and `100`. Defaults to `90`. Only relevant if the format is set to `jpg` or `pjpg`.

~~~ html
<img src="kayaks.jpg?w=500&q=25">
~~~

![© Photo Joel Reynolds](/images/kayaks-q-25_w-500.jpg?w=500&q=25)


## Format `fm`

Encodes the image to a specific format. Accepts `jpg`, `pjpg` (progressive jpeg), `png`, `gif` or `webp`. Defaults to `jpg`.

~~~ html
<img src="kayaks.jpg?w=500&fm=gif">
~~~

![© Photo Joel Reynolds](/images/kayaks-fm-gif_w-500.gif?w=500&fm=gif)
