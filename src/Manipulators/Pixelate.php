<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Pixelate extends BaseManipulator
{
    /**
     * Perform pixelate image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $pixelate = $this->getPixelate();

        if (null !== $pixelate) {
            $image->pixelate($pixelate);
        }

        return $image;
    }

    /**
     * Resolve pixelate amount.
     *
     * @return int|null The resolved pixelate amount.
     */
    public function getPixelate(): ?int
    {
        $pixel = $this->getParam('pixel');

        if (!is_numeric($pixel)
            || $pixel < 0
            || $pixel > 1000
        ) {
            return null;
        }

        return (int) $pixel;
    }
}
