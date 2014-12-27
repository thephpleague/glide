<?php

namespace Glide\Manipulators;

use Glide\Request;
use Intervention\Image\Image;

interface ManipulatorInterface
{
    public function validate(Request $request, Image $image);
    public function run(Request $request, Image $image);
}
