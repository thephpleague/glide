<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $or
 */
class Orientation extends BaseManipulator
{
    /**
     * Perform orientation image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        $orientation = $this->getOrientation();

        if ($orientation === 'auto') {
            return $image->orientate();
        }

        return $image->rotate((int) $orientation);
    }

    /**
     * Resolve orientation.
     * @return string The resolved orientation.
     */
    public function getOrientation(): string
    {
        if (in_array($this->or, ['auto', '0', '90', '180', '270'], true)) {
            return $this->or;
        }

        return 'auto';
    }
}
