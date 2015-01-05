<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Interfaces\Manipulator;
use League\Glide\ImageRequest;

class Sharpen implements Manipulator
{
    /**
     * Perform sharpen image manipulation.
     * @param ImageRequest $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(ImageRequest $request, Image $image)
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
