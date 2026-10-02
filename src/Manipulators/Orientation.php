<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Orientation extends BaseManipulator
{
    public function getApiParams(): array
    {
        return ['or'];
    }

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

        if ($orientation === 'auto') {
            // The decoder already aligned the image (only GD resets the EXIF orientation afterwards).
            if ($image->driver()->config()->autoOrientation) {
                return $image;
            }

            // Skip upright images: on vips, orient() always renders the image into memory.
            $exifOrientation = $image->exif('IFD0.Orientation');

            return is_numeric($exifOrientation) && (int) $exifOrientation > 1 ? $image->orient() : $image;
        }

        // A 0° rotation is a no-op, but drivers still copy the full canvas (GD: 2-3x slower).
        if ($orientation === '0') {
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
        $or = (string) $this->getParam('or');

        if (in_array($or, ['0', '90', '180', '270'], true)) {
            return $or;
        }

        return 'auto';
    }
}
