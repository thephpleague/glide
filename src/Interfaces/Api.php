<?php

namespace Glide\Interfaces;

use Glide\Request;

interface Api
{
    /**
     * Perform image manipulations.
     * @param  Request $request The request object.
     * @param  string  $source  Source image binary data.
     * @return string  Manipulated image binary data.
     */
    public function run(Request $request, $source);
}
