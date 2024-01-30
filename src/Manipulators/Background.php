<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Origin;
use League\Glide\Manipulators\Helpers\Color;

class Background extends BaseManipulator
{
    /**
     * Perform background image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $bg = (string) $this->getParam('bg');

        if ('' === $bg) {
            return $image;
        }

        $color = (new Color($bg))->formatted();

        return $image->driver()->createImage($image->width(), $image->height())
            ->fill($color)
            ->place($image, 'top-left', 0, 0)
            ->setOrigin(
                new Origin($image->origin()->mediaType())
            );
    }
}
