<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Origin;
use League\Glide\Manipulators\Helpers\Color;

/**
 * @property string $bg
 */
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
        if (is_null($this->bg)) {
            return $image;
        }

        $color = (new Color($this->bg))->formatted();

        if ($color) {
            $new = $image->driver()->createImage($image->width(), $image->height())
                ->fill($color)
                ->place($image, 'top-left', 0, 0)
                ->setOrigin(
                    new Origin($image->origin()->mediaType())
                );

            $image = $new;
        }

        return $image;
    }
}
