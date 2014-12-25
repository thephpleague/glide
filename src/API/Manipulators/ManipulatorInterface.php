<?php

namespace Glide\API\Manipulators;

use Intervention\Image\Image;

interface ManipulatorInterface
{
    public function run(Image $image);
}
