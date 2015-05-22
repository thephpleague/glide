<?php

namespace League\Glide\Http;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class RequestArgumentsResolver
{
    /**
     * Resolve request object.
     * @param  array   $args Array of supplied arguments.
     * @return Request The request object.
     */
    public function getRequest($args)
    {
        if (isset($args[0]) and $args[0] instanceof Request) {
            return $args[0];
        }

        if (isset($args[0]) and is_string($args[0])) {
            $path = $args[0];
            $params = [];

            if (isset($args[1]) and is_array($args[1])) {
                $params = $args[1];
            }

            return RequestFactory::create($path, $params);
        }

        throw new InvalidArgumentException('Not a valid path or Request object.');
    }
}
