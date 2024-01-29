<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Brightness extends BaseManipulator
{
    /**
     * Perform brightness image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $brightness = $this->getBrightness();

        if (null !== $brightness) {
            $image->brightness($brightness);
        }

        return $image;
    }

    /**
     * Resolve brightness amount.
     *
     * @return int|null The resolved brightness amount.
     */
    public function getBrightness(): ?int
    {
        $bri = (string) $this->getParam('bri');

        if ('' === $bri
            or !preg_match('/^-*[0-9]+$/', $bri)
            or $bri < -100
            or $bri > 100
        ) {
            return null;
        }

        return (int) $bri;
    }
}
