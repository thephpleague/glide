<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

/**
 * @property string $pixel
 */
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
    public function getPixelate()
    {
        if (!is_numeric($this->pixel)) {
            return;
        }

        if ($this->pixel < 0 or $this->pixel > 1000) {
            return;
        }

        return (int) $this->pixel;
    }
}
