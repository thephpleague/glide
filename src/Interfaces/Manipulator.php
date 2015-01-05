<?php

namespace League\Glide\Interfaces;

use Intervention\Image\Image;
use League\Glide\ImageRequest;

interface Manipulator
{
    /**
     * Perform image manipulations.
     * @param ImageRequest $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(ImageRequest $request, Image $image);
}
