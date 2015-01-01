<?php

namespace Glide\Interfaces;

use Glide\Request;
use Intervention\Image\Image;

interface Manipulator
{
    /**
     * Perform image manipulations.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return null
     */
    public function run(Request $request, Image $image);
}
