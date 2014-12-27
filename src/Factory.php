<?php

namespace Glide;

use Intervention\Image\ImageManager;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Factory
{
    public static function server(Array $config)
    {
        if (is_string($config['source'])) {
            $config['source'] = new Filesystem(new Local($config['source']));
        }

        if (is_string($config['cache'])) {
            $config['cache'] = new Filesystem(new Local($config['cache']));
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
            new Manipulator(
                new ImageManager([
                    'driver' => $config['driver']
                ]),
                [
                    new Manipulators\Adjustments(),
                    new Manipulators\Size($config['max_image_size']),
                    new Manipulators\Effects(),
                    new Manipulators\Output(),
                ]
            )
        );

        if (isset($config['sign_key'])) {
            $server->setSignKey($config['sign_key']);
        }

        return $server;
    }
}
