<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Gamma implements ManipulatorInterface
{
    /**
     * Perform gamma image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $gamma = $this->getGamma($request->get('gam'));

        if ($gamma) {
            $image->gamma($gamma);
        }

        return $image;
    }

    /**
     * Resolve gamma amount.
     * @param  string $gamma The gamma amount.
     * @return string The resolved gamma amount.
     */
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
