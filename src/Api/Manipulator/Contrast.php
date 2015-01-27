<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Contrast implements ManipulatorInterface
{
    /**
     * Perform contrast image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $contrast = $this->getContrast($request->get('con'));

        if ($contrast) {
            $image->contrast($contrast);
        }

        return $image;
    }

    /**
     * Resolve contrast amount.
     * @param  string $contrast The contrast amount.
     * @return string The resolved contrast amount.
     */
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
