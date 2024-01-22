<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

/**
 * @property string $sharp
 */
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
    public function getSharpen()
    {
        if (!is_numeric($this->sharp)) {
            return;
        }

        if ($this->sharp < 0 or $this->sharp > 100) {
            return;
        }

        return (int) $this->sharp;
    }
}
