<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $filt
 */
class Filter extends BaseManipulator
{
    /**
     * Perform filter image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        if ($this->filt === 'greyscale') {
            return $this->runGreyscaleFilter($image);
        }

        if ($this->filt === 'sepia') {
            return $this->runSepiaFilter($image);
        }

        return $image;
    }

    /**
     * Perform greyscale manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function runGreyscaleFilter(Image $image): Image
    {
        return $image->greyscale();
    }

    /**
     * Perform sepia manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function runSepiaFilter(Image $image): Image
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
