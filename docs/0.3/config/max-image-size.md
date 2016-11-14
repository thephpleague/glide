---
layout: default
title: Max image size
---

# Max image size

In addition to [securing images](/config/secure-images/) with a signing key, you can also limit how large images can be generated. The following setting will set the maximum allowed total image size, in pixels.

~~~ php
<?php

$server = League\Glide\ServerFactory::create([
    'max_image_size' => 2000*2000,
]);
~~~

Notice that Glide doesn't actually restrict the width or height, but rather the total image size. In the above example it would be `4000000px`. This accomplishes the exact same thing, while offering more flexibility with your image sizes.