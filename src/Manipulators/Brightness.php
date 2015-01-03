<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Interfaces\Manipulator;
use League\Glide\Request;

class Brightness implements Manipulator
{
    /**
     * Perform brightness image manipulation.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(Request $request, Image $image)
    {
        $brightness = $this->getBrightness($request->getParam('bri'));

        if ($brightness) {
            $image->brightness($brightness);
        }
    }

    /**
     * Resolve brightness amount.
     * @param  string $brightness The brightness amount.
     * @return string The resolved brightness amount.
     */
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
