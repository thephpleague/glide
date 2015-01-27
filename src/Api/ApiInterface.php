<?php

namespace League\Glide\Api;

use Symfony\Component\HttpFoundation\Request;

interface ApiInterface
{
    /**
     * Perform image manipulations.
     * @param  Request $request The request object.
     * @param  string  $source  Source image binary data.
     * @return string  Manipulated image binary data.
     */
    public function run(Request $request, $source);
}
