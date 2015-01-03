<?php

namespace League\Glide;

use League\Glide\Factories\Server as ServerFactory;

class Factory
{
    /**
     * Create server instance.
     * @param  Array  $config Configuration parameters.
     * @return Server The configured server.
     */
    public static function server(Array $config = [])
    {
        return (new ServerFactory($config))->make();
    }
}
