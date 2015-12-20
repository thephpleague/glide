<?php

$loader = require '../vendor/autoload.php';

// Set manipulators
$manipulators = [
    new League\Glide\Manipulators\Size(2000*2000),
    new League\Glide\Manipulators\Encode(),
];

// Set image manager
$imageManager = new Intervention\Image\ImageManager([
    'driver' => 'imagick',
]);

// Set API
$api = new League\Glide\Api\Api($imageManager, $manipulators);

// Set image source
$source = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('/var/lib/glide/master')
);

// Set image cache
$cache = new League\Flysystem\Filesystem(
    new League\Flysystem\Adapter\Local('/var/lib/glide/cache')
);

// Setup Glide server
$server = new League\Glide\Server($source, $cache, $api);

$urlComponents = parse_url($_SERVER['REQUEST_URI']);
parse_str($urlComponents['query'], $query);
$server->outputImage($urlComponents['path'], $query);
