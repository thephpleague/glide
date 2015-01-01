<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Blur implements Manipulator
{
    /**
     * Perform blur image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return null
     */
    public function run(Request $request, Image $image)
    {
        $blur = $this->getBlur($request->getParam('blur'));

        if ($blur) {
            $image->blur($blur);
        }
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
