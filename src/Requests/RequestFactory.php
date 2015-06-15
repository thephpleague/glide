<?php

namespace League\Glide\Requests;

use Symfony\Component\HttpFoundation\Request;

class RequestFactory
{
    /**
     * Create a request instance.
     * @param  max     $args                 The request object or path/params combination.
     * @param  array   $defaultManipulations The default image manipulations.
     * @return Request The request object.
     */
    public static function create($args, $defaultManipulations = [])
    {
        if (isset($args[0]) and $args[0] instanceof Request) {
            $request = $args[0];
        }

        if (isset($args[0]) and is_string($args[0])) {
            $params = [];

            if (isset($args[1]) and is_array($args[1])) {
                $params = $args[1];
            }

            $request = new Request($params, [], [], [], [], array_merge($_SERVER, ['REQUEST_URI' => $args[0]]));
        }

        if (!isset($request)) {
            throw new InvalidArgumentException('Not a valid path or Request object.');
        }

        foreach ($defaultManipulations as $key => $value) {
            if (!$request->get($key)) {
                $request->query->set($key, $value);
            }
        }

        return $request;
    }
}
