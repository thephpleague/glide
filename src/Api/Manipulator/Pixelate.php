<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Pixelate implements ManipulatorInterface
{
    /**
     * Perform pixelate image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $pixelate = $this->getPixelate($request->get('pixel'));

        if ($pixelate) {
            $image->pixelate($pixelate);
        }

        return $image;
    }

    /**
     * Resolve pixelate amount.
     * @param  string $pixelate The pixelate amount.
     * @return string The resolved pixelate amount.
     */
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
