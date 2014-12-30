<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Blur implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $blur = $this->getBlur($request->blur);

        if ($blur) {
            $image->blur($blur);
        }
    }

    public function getBlur($blur)
    {
        if (is_null($blur)) {
            return false;
        }

        if (!ctype_digit($blur)) {
            return false;
        }

        if ($blur < 0 or $blur > 100) {
            return false;
        }

        return $blur;
    }
}
