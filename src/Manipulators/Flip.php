<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $or
 */
class Flip extends BaseManipulator
{
    /**
     * Perform flip image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image)
    {
        if ($flip = $this->getFlip()) {
            if ($flip === 'all') {
                return $image->flip('h')->flip('v');
            }

            return $image->flip($flip);
        }

        return $image;
    }

    /**
     * Resolve flip.
     * @return string The resolved flip.
     */
    public function getFlip()
    {
        if (in_array($this->flip, ['h', 'v', 'all'], true)) {
            return $this->flip;
        }
    }
}
