<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

/**
 * @property string|null $bri
 */
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
        if (null === $this->bri || !preg_match('/^-*[0-9]+$/', $this->bri)) {
            return null;
        }

        if ($this->bri < -100 or $this->bri > 100) {
            return null;
        }

        return (int) $this->bri;
    }
}
