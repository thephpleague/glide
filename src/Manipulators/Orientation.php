<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Interfaces\Manipulator;
use League\Glide\Request;

class Orientation implements Manipulator
{
    /**
     * Perform orientation image manipulation.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(Request $request, Image $image)
    {
        $orientation = $this->getOrientation($request->getParam('or'));

        if ($orientation === 'auto') {
            $image->orientate();
        }

        if (in_array($orientation, ['90', '180', '270'], true)) {
            $image->rotate($orientation);
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
