---
layout: default
permalink: config/max-image-size/
title: Max Image Size
---

# Max Image Size

If you're not [securing images](/config/secure-images/) with a signing key, you can choose to limit how large images can be generated. The following setting will set the maximum allowed total image size, in pixels.

~~~ php
use League\Glide\Factories\Server;

// Set max image size in Glide configuration
$glide = Server::create([
    'max_image_size' => 2000*2000,
]);
~~~