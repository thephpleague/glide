<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Brightness implements ManipulatorInterface
{
    /**
     * Perform brightness image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $brightness = $this->getBrightness($request->get('bri'));

        if ($brightness) {
            $image->brightness($brightness);
        }

        return $image;
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
