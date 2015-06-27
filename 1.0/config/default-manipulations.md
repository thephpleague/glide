---
layout: default
title: Default manipulations
---

# Default manipulations

In certain situations you may want to define default image manipulations. For example, maybe you want to specify that all images are outputted as JPEGs (`fm=jpg`). Or maybe you have a watermark that you want added to all images. Glide makes this possible using default manipulations.

~~~ php
// Set using factory
$server = League\Glide\ServerFactory::create([
    'default_manipulations' => [
        'mark' => 'logo.png',
        'markw' => '30w',
        'markpad' => '5w',
    ]
]);

// Set using setter method
$server->setDefaultManipulations([
    'mark' => 'logo.png',
    'markw' => '30w',
    'markpad' => '5w',
]);
~~~

## Overriding default manipulations

You can override the default manipulations for a specific request by passing a new parameter (e.x. `mark=different-logo.png`), or even disable it entirely by setting it to blank (e.x. `mark=`).