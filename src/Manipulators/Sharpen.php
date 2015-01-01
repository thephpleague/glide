<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Sharpen implements Manipulator
{
    /**
     * Perform sharpen image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return null
     */
    public function run(Request $request, Image $image)
    {
        $sharpen = $this->getSharpen($request->getParam('sharp'));

        if ($sharpen) {
            $image->sharpen($sharpen);
        }
    }

    /**
     * Resolve sharpen amount.
     * @param  string $sharpen The sharpen amount.
     * @return string The resolved sharpen amount.
     */
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
