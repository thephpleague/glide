<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Contrast implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $contrast = $this->getContrast($request->con);

        if ($contrast) {
            $image->contrast($contrast);
        }
    }

    public function getContrast($contrast)
    {
        if (is_null($contrast)) {
            return false;
        }

        if (!preg_match('/^-*[0-9]+$/', $contrast)) {
            return false;
        }

        if ($contrast < -100 or $contrast > 100) {
            return false;
        }

        return $contrast;
    }
}
