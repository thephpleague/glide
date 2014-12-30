<?php

namespace Glide\Interfaces;

use Glide\Request;
use Intervention\Image\Image;

interface Manipulator
{
    public function run(Request $request, Image $image);
}
