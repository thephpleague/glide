<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Blur extends BaseManipulator
{
    /**
     * Perform blur image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $blur = $this->getBlur();

        if (null !== $blur) {
            $image->blur($blur);
        }

        return $image;
    }

    /**
     * Resolve blur amount.
     *
     * @return int|null The resolved blur amount.
     */
    public function getBlur(): ?int
    {
        $blur = $this->getParam('blur');

        if (!is_numeric($blur)
            || $blur < 0
            || $blur > 100
        ) {
            return null;
        }

        return (int) $blur;
    }
}
