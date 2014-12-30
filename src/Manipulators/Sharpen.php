<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Sharpen implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $sharpen = $this->getSharpen($request->sharp);

        if ($sharpen) {
            $image->sharpen($sharpen);
        }
    }

    public function getSharpen($sharpen)
    {
        if (is_null($sharpen)) {
            return false;
        }

        if (!ctype_digit($sharpen)) {
            return false;
        }

        if ($sharpen < 0 or $sharpen > 100) {
            return false;
        }

        return $sharpen;
    }
}
