<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Sharpen extends BaseManipulator
{
    /**
     * Perform sharpen image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $sharpen = $this->getSharpen();

        if (null !== $sharpen) {
            $image->sharpen($sharpen);
        }

        return $image;
    }

    /**
     * Resolve sharpen amount.
     *
     * @return int|null The resolved sharpen amount.
     */
    public function getSharpen(): ?int
    {
        $sharp = $this->getParam('sharp');

        if (!is_numeric($sharp)
            || $sharp < 0
            || $sharp > 100
        ) {
            return null;
        }

        return (int) $sharp;
    }
}
