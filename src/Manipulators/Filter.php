<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Filter extends BaseManipulator
{
    /**
     * Perform filter image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        return match ($this->getParam('filt')) {
            'greyscale' => $this->runGreyscaleFilter($image),
            'sepia' => $this->runSepiaFilter($image),
            default => $image,
        };
    }

    /**
     * Perform greyscale manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runGreyscaleFilter(ImageInterface $image): ImageInterface
    {
        return $image->greyscale();
    }

    /**
     * Perform sepia manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runSepiaFilter(ImageInterface $image): ImageInterface
    {
        $image->greyscale()
            ->brightness(-10)
            ->contrast(10)
            ->colorize(38, 27, 12)
            ->brightness(-10)
            ->contrast(10);

        return $image;
    }
}
