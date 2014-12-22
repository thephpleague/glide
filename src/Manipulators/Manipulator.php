<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

interface Manipulator
{
    public function run(Image $image);
}
