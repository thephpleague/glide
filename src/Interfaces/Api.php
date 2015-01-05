<?php

namespace League\Glide\Interfaces;

use League\Glide\ImageRequest;

interface Api
{
    /**
     * Perform image manipulations.
     * @param  ImageRequest $request The request object.
     * @param  string  $source  Source image binary data.
     * @return string  Manipulated image binary data.
     */
    public function run(ImageRequest $request, $source);
}
