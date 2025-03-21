<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\Helpers\Color;

class Background extends BaseManipulator
{
    public function getApiParams(): array
    {
        return ['bg'];
    }

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

        return $image->blendTransparency($color);
    }
}
