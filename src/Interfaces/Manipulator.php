<?php

namespace League\Glide\Interfaces;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

interface Manipulator
{
    /**
     * Perform image manipulations.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image);
}
