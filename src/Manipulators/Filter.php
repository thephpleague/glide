<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Interfaces\Manipulator;
use League\Glide\Request;

class Filter implements Manipulator
{
    /**
     * Perform filter image manipulation.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(Request $request, Image $image)
    {
        if ($request->getParam('filt') === 'greyscale') {
            $this->runGreyscaleFilter($image);
        }

        if ($request->getParam('filt') === 'sepia') {
            $this->runSepiaFilter($image);
        }
    }

    /**
     * Perform greyscale manipulation.
     * @param Image $image The source image.
     */
    public function runGreyscaleFilter(Image $image)
    {
        $image->greyscale();
    }

    /**
     * Perform sepia manipulation.
     * @param Image $image The source image.
     */
    public function runSepiaFilter(Image $image)
    {
        $image->greyscale();
        $image->brightness(-10);
        $image->contrast(10);
        $image->colorize(38, 27, 12);
        $image->brightness(-10);
        $image->contrast(10);
    }
}
