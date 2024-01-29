<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Contrast extends BaseManipulator
{
    /**
     * Perform contrast image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $contrast = $this->getContrast();

        if (null !== $contrast) {
            $image->contrast($contrast);
        }

        return $image;
    }

    /**
     * Resolve contrast amount.
     *
     * @return int|null The resolved contrast amount.
     */
    public function getContrast(): ?int
    {
        $con = (string) $this->getParam('con');

        if ('' === $con
            || !preg_match('/^-*[0-9]+$/', $con)
            || $con < -100
            || $con > 100
        ) {
            return null;
        }

        return (int) $con;
    }
}
