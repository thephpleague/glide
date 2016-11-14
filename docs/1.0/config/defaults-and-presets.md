---
layout: default
title: Defaults & presets
---

# Defaults & presets

In certain situations you may want to define default image manipulations. For example, maybe you want to specify that all images are outputted as JPEGs (`fm=jpg`). Or maybe you have a watermark that you want added to all images. Glide makes this possible using default manipulations.

~~~ php
<?php

// Set using factory
$server = League\Glide\ServerFactory::create([
    'defaults' => [
        'mark' => 'logo.png',
        'markw' => '30w',
        'markpad' => '5w',
    ]
]);

// Set using setter method
$server->setDefaults([
    'mark' => 'logo.png',
    'markw' => '30w',
    'markpad' => '5w',
]);
~~~

## Presets

Glide also makes it possible to define groups of defaults, known as presets. This is helpful if you have standard image manipulations that you use throughout your app.

### Configuring presets

~~~ php
<?php

// Set using factory
$server = League\Glide\ServerFactory::create([
    'presets' => [
        'small' => [
            'w' => 200,
            'h' => 200,
            'fit' => 'crop',
        ],
        'medium' => [
            'w' => 600,
            'h' => 400,
            'fit' => 'crop',
        ]
    ]
]);

// Set using setter method
$server->setPresets([
    'small' => [
        'w' => 200,
        'h' => 200,
        'fit' => 'crop',
    ],
    'medium' => [
        'w' => 600,
        'h' => 400,
        'fit' => 'crop',
    ]
]);
~~~

### Using presets

To use a presets, set it using the `p` parameter:

~~~ html
<img src="kayaks.jpg?p=small">
~~~

It's also possible to use multiple presets together:

~~~ html
<img src="kayaks.jpg?p=small,watermarked">
~~~

It's even possible to use presets with additional parameters:

~~~ html
<img src="kayaks.jpg?p=small,watermarked&filt=sepia">
~~~

## Overriding defaults and presets

You can override the default and preset manipulations for a specific request by passing a new parameter (e.x. `mark=different.png`), or even disable it entirely by setting it to blank (e.x. `mark=`).