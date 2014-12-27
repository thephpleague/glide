<?php

namespace Glide\Interfaces;

use Glide\Request;
use Intervention\Image\Image;

interface Manipulator
{
    public function validate(Request $request, Image $image);
    public function run(Request $request, Image $image);
}
