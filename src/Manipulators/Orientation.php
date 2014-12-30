<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Orientation implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $orientation = $this->getOrientation($request->or);

        if ($orientation === 'auto') {
            $image->orientate();
        }

        if (in_array($orientation, ['90', '180', '270'])) {
            $image->rotate($orientation);
        }
    }

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
