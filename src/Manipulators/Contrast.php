<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $con
 */
class Contrast extends BaseManipulator
{
    /**
     * Perform contrast image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        $contrast = $this->getContrast();

        if ($contrast !== null) {
            $image->contrast($contrast);
        }

        return $image;
    }

    /**
     * Resolve contrast amount.
     * @return int|null The resolved contrast amount.
     */
    public function getContrast(): ?int
    {
        if (!preg_match('/^-*[0-9]+$/', (string) $this->con)) {
            return null;
        }

        if ($this->con < -100 or $this->con > 100) {
            return null;
        }

        return (int) $this->con;
    }
}
