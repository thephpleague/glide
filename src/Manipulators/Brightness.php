<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $bri
 */
class Brightness extends BaseManipulator
{
    /**
     * Perform brightness image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        $brightness = $this->getBrightness();

        if ($brightness !== null) {
            $image->brightness($brightness);
        }

        return $image;
    }

    /**
     * Resolve brightness amount.
     * @return int|null The resolved brightness amount.
     */
    public function getBrightness(): ?int
    {
        if (!preg_match('/^-*[0-9]+$/', (string) $this->bri)) {
            return null;
        }

        if ($this->bri < -100 or $this->bri > 100) {
            return null;
        }

        return (int) $this->bri;
    }
}
