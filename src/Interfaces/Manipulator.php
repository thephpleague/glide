<?php

namespace League\Glide\Interfaces;

use Intervention\Image\Image;
use League\Glide\Request;

interface Manipulator
{
    /**
     * Perform image manipulations.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(Request $request, Image $image);
}
