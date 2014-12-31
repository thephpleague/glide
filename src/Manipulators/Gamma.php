<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Gamma implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $gamma = $this->getGamma($request->getParam('gam'));

        if ($gamma) {
            $image->gamma($gamma);
        }
    }

    public function getGamma($gamma)
    {
        if (is_null($gamma)) {
            return false;
        }

        if (!preg_match('/^[0-9]\.*[0-9]*$/', $gamma)) {
            return false;
        }

        if ($gamma < 0.1 or $gamma > 9.99) {
            return false;
        }

        return $gamma;
    }
}
