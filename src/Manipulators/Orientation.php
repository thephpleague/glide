<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Interfaces\Manipulator;
use Symfony\Component\HttpFoundation\Request;

class Orientation implements Manipulator
{
    /**
     * Perform orientation image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $orientation = $this->getOrientation($request->get('or'));

        if ($orientation === 'auto') {
            return $image->orientate();
        }

        if (in_array($orientation, ['90', '180', '270'], true)) {
            return $image->rotate($orientation);
        }
    }

    /**
     * Resolve orientation.
     * @param  string $orientation The orientation.
     * @return string The resolved orientation.
     */
    public function getOrientation($orientation)
    {
        if (is_null($orientation)) {
            return 'auto';
        }

        if (!in_array($orientation, ['auto', '0', '90', '180', '270'], true)) {
            return 'auto';
        }

        return $orientation;
    }
}
