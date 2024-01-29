<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Gamma extends BaseManipulator
{
    /**
     * Perform gamma image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $gamma = $this->getGamma();

        if (null !== $gamma) {
            $image->gamma($gamma);
        }

        return $image;
    }

    /**
     * Resolve gamma amount.
     *
     * @return float|null The resolved gamma amount.
     */
    public function getGamma(): ?float
    {
        $gam = (string) $this->getParam('gam');

        if ('' === $gam
            || !preg_match('/^[0-9]\.*[0-9]*$/', $gam)
            || $gam < 0.1
            || $gam > 9.99
        ) {
            return null;
        }

        return (float) $gam;
    }
}
