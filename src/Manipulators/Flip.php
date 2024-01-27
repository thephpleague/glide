<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

/**
 * @property string $flip
 */
class Flip extends BaseManipulator
{
    /**
     * Perform flip image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $flip = $this->getFlip();
        if (null !== $flip) {
            if ('both' === $flip) {
                return $image->flip()->flop();
            }

            if ('v' === $flip) {
                return $image->flip();
            }

            if ('h' === $flip) {
                return $image->flop();
            }
        }

        return $image;
    }

    /**
     * Resolve flip.
     *
     * @return string|null The resolved flip.
     */
    public function getFlip(): ?string
    {
        if (in_array($this->flip, ['h', 'v', 'both'], true)) {
            return $this->flip;
        }

        return null;
    }
}
