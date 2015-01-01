<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Orientation implements Manipulator
{
    /**
     * Perform orientation image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $source  The source image.
     * @return null
     */
    public function run(Request $request, Image $image)
    {
        $orientation = $this->getOrientation($request->getParam('or'));

        if ($orientation === 'auto') {
            $image->orientate();
        }

        if (in_array($orientation, ['90', '180', '270'])) {
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

        if (!in_array($orientation, ['auto', '0', '90', '180', '270'])) {
            return 'auto';
        }

        return $orientation;
    }
}
