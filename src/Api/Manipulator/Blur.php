<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Blur implements ManipulatorInterface
{
    /**
     * Perform blur image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $blur = $this->getBlur($request->get('blur'));

        if ($blur) {
            $image->blur($blur);
        }

        return $image;
    }

    /**
     * Resolve blur amount.
     * @param  string $blur The blur amount.
     * @return string The resolved blur amount.
     */
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
