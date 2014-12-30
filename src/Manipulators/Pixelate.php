<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Pixelate implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $pixelate = $this->getPixelate($request->pixel);

        if ($pixelate) {
            $image->pixelate($pixelate);
        }
    }

    public function getPixelate($pixelate)
    {
        if (is_null($pixelate)) {
            return false;
        }

        if (!ctype_digit($pixelate)) {
            return false;
        }

        if ($pixelate < 0 or $pixelate > 1000) {
            return false;
        }

        return $pixelate;
    }
}
