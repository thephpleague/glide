<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $blur
 */
class Blur extends BaseManipulator
{
    /**
     * Perform blur image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        $blur = $this->getBlur();

        if ($blur !== null) {
            $image->blur($blur);
        }

        return $image;
    }

    /**
     * Resolve blur amount.
     * @return int|null The resolved blur amount.
     */
    public function getBlur(): ?int
    {
        if (!is_numeric($this->blur)) {
            return null;
        }

        if ($this->blur < 0 or $this->blur > 100) {
            return null;
        }

        return (int) $this->blur;
    }
}
