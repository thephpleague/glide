<?php

namespace Glide;

use Glide\API\API;
use Intervention\Image\ImageManager;

class Factory
{
    public static function server(Array $config)
    {
        if (!isset($config['source'])) {
            $config['source'] = null;
        }

        if (!isset($config['cache'])) {
            $config['cache'] = null;
        }

        if (!isset($config['driver'])) {
            $config['driver'] = 'gd';
        }

        if (!isset($config['max_image_size'])) {
            $config['max_image_size'] = null;
        }

        $server = new Server(
            $config['source'],
            $config['cache'],
            new API(
                new ImageManager(['driver' => $config['driver']]),
                $config['max_image_size']
            )
        );

        if (isset($config['sign_key'])) {
            $server->setSignKey($config['sign_key']);
        }

        return $server;
    }
}
