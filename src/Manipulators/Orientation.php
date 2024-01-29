<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

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
        $originalOrientation = $image->exif('Orientation');

        if ('auto' === $orientation && is_numeric($originalOrientation)) {
            switch ($originalOrientation) {
                case 2:
                    $image->flip();
                    break;
                case 3:
                    $image->rotate(180);
                    break;
                case 4:
                    $image->rotate(180)->flip();
                    break;
                case 5:
                    $image->rotate(270)->flip();
                    break;
                case 6:
                    $image->rotate(270);
                    break;
                case 7:
                    $image->rotate(90)->flip();
                    break;
                case 8:
                    $image->rotate(90);
                    break;
            }

            return $image;
        }

        return $image->rotate((float) $orientation);
    }

    /**
     * Resolve orientation.
     *
     * @return string The resolved orientation.
     */
    public function getOrientation(): string
    {
        $or = $this->getParam('or');

        if (in_array($or, ['auto', '0', '90', '180', '270'], true)) {
            return $or;
        }

        return 'auto';
    }
}
