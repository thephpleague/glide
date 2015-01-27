<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Sharpen implements ManipulatorInterface
{
    /**
     * Perform sharpen image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $sharpen = $this->getSharpen($request->get('sharp'));

        if ($sharpen) {
            $image->sharpen($sharpen);
        }

        return $image;
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
