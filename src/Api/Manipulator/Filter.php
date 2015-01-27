<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Filter implements ManipulatorInterface
{
    /**
     * Perform filter image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        if ($request->get('filt') === 'greyscale') {
            return $this->runGreyscaleFilter($image);
        }

        if ($request->get('filt') === 'sepia') {
            return $this->runSepiaFilter($image);
        }

        return $image;
    }

    /**
     * Perform greyscale manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function runGreyscaleFilter(Image $image)
    {
        return $image->greyscale();
    }

    /**
     * Perform sepia manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function runSepiaFilter(Image $image)
    {
        $image->greyscale();
        $image->brightness(-10);
        $image->contrast(10);
        $image->colorize(38, 27, 12);
        $image->brightness(-10);
        $image->contrast(10);

        return $image;
    }
}
