<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

/**
 * @property string $or
 */
class Orientation extends BaseManipulator
{
    /**
     * Perform orientation image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $orientation = $this->getOrientation();

        if ('auto' === $orientation) {
            return $image->orientate();
        }

        return $image->rotate((float) $orientation);
    }

    /**
     * Resolve orientation.
     *
     * @return string The resolved orientation.
     */
    public function getOrientation()
    {
        if (in_array($this->or, ['auto', '0', '90', '180', '270'], true)) {
            return $this->or;
        }

        return 'auto';
    }
}
