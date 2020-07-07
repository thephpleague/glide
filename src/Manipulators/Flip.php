<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $flip
 */
class Flip extends BaseManipulator
{
    /**
     * Perform flip image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        if ($flip = $this->getFlip()) {
            if ($flip === 'both') {
                return $image->flip('h')->flip('v');
            }

            return $image->flip($flip);
        }

        return $image;
    }

    /**
     * Resolve flip.
     * @return null|string The resolved flip.
     */
    public function getFlip(): ?string
    {
        if (in_array($this->flip, ['h', 'v', 'both'], true)) {
            return $this->flip;
        }

        return null;
    }
}
