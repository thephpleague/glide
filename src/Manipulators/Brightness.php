<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Brightness implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $brightness = $this->getBrightness($request->getParam('bri'));

        if ($brightness) {
            $image->brightness($brightness);
        }
    }

    public function getBrightness($brightness)
    {
        if (is_null($brightness)) {
            return false;
        }

        if (!preg_match('/^-*[0-9]+$/', $brightness)) {
            return false;
        }

        if ($brightness < -100 or $brightness > 100) {
            return false;
        }

        return $brightness;
    }
}
