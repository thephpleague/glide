<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

interface ManipulatorInterface
{
    /**
     * Perform image manipulations.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params);
}
