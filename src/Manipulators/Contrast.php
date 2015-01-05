<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Interfaces\Manipulator;
use Symfony\Component\HttpFoundation\Request;

class Contrast implements Manipulator
{
    /**
     * Perform contrast image manipulation.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(Request $request, Image $image)
    {
        $contrast = $this->getContrast($request->get('con'));

        if ($contrast) {
            $image->contrast($contrast);
        }
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
