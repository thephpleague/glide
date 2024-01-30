<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

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
            return match ($flip) {
                'both' => $image->flip()->flop(),
                'v' => $image->flip(),
                'h' => $image->flop(),
                default => $image,
            };
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
        $flip = $this->getParam('flip');

        if (in_array($flip, ['h', 'v', 'both'], true)) {
            return $flip;
        }

        return null;
    }
}
