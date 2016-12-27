---
layout: default
title: Laravel integration
---

# Laravel integration

If your application uses the [Laravel](https://laravel.com/) framework, you can use the `LaravelResponseFactory`. Since Laravel uses `HttpFoundation` under the hood, this adapter actually extends the [Symfony adapter](/1.0/config/integrations/symfony/).

<p class="message-notice">This adapter requires Laravel 4 or newer.</p>

## Installation

~~~ bash
composer require league/glide-laravel
~~~

## Configuration

~~~ php
<?php

use League\Glide\ServerFactory;
use League\Glide\Responses\LaravelResponseFactory;

$server = ServerFactory::create([
    'response' => new LaravelResponseFactory(app('request'))
]);
~~~

## Working example

Here is a fully functioning example of how to get Glide up and running with Laravel really quick. First, create a new entry in your routes file:

~~~ php
<?php

Route::get('/img/{path}', 'ImageController@show')->where('path', '.*');
~~~

Next, create a controller that will serve all your Glide images. This will use the default Laravel storage path (`/storage/app`) for the source images.

~~~ php
<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class ImageController extends Controller
{
    public function show(Filesystem $filesystem, $path)
    {
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'img',
        ]);

        return $server->getImageResponse($path, request()->all());
    }
}
~~~
