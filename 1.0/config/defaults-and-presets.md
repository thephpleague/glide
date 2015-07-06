---
layout: default
title: Defaults & presets
---

# Defaults & presets

In certain situations you may want to define default image manipulations. For example, maybe you want to specify that all images are outputted as JPEGs (`fm=jpg`). Or maybe you have a watermark that you want added to all images. Glide makes this possible using default manipulations.

~~~ php
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
// Set using factory
$server = League\Glide\ServerFactory::create([
    'presets' => [
        'small' = [
            'w' => 200,
            'h' => 200,
            'fit' => 'crop',
        ],
        'medium' = [
            'w' => 600,
            'h' => 400,
            'fit' => 'crop',
        ]
    ]
]);

// Set using setter method
$server->setPresets([
    'small' = [
        'w' => 200,
        'h' => 200,
        'fit' => 'crop',
    ],
    'medium' = [
        'w' => 600,
        'h' => 400,
        'fit' => 'crop',
    ]
]);
~~~

### Using presets

To actually use the presets, use the `p` function to set the preset:

~~~ html
<img src="kayaks.jpg?p=small">
~~~

## Overriding defaults and presets

You can override the default and preset manipulations for a specific request by passing a new parameter (e.x. `mark=different.png`), or even disable it entirely by setting it to blank (e.x. `mark=`).