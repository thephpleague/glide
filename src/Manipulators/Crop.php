<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

class Crop extends BaseManipulator
{
    /**
     * Perform crop image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $coordinates = $this->getCoordinates($image);

        if ($coordinates) {
            $coordinates = $this->limitToImageBoundaries($image, $coordinates);

            $image->crop(
                $coordinates[0],
                $coordinates[1],
                $coordinates[2],
                $coordinates[3]
            );
        }

        return $image;
    }

    /**
     * Resolve coordinates.
     *
     * @param ImageInterface $image The source image.
     *
     * @return int[]|null The resolved coordinates.
     *
     * @psalm-return array{0: int, 1: int, 2: int, 3: int}|null
     */
    public function getCoordinates(ImageInterface $image): ?array
    {
        $crop = (string) $this->getParam('crop');

        if ('' === $crop) {
            return null;
        }

        $coordinates = explode(',', $crop);

        if (4 !== count($coordinates)
            || (!is_numeric($coordinates[0]))
            || (!is_numeric($coordinates[1]))
            || (!is_numeric($coordinates[2]))
            || (!is_numeric($coordinates[3]))
            || ($coordinates[0] <= 0)
            || ($coordinates[1] <= 0)
            || ($coordinates[2] < 0)
            || ($coordinates[3] < 0)
            || ($coordinates[2] >= $image->width())
            || ($coordinates[3] >= $image->height())) {
            return null;
        }

        return [
            (int) $coordinates[0],
            (int) $coordinates[1],
            (int) $coordinates[2],
            (int) $coordinates[3],
        ];
    }

    /**
     * Limit coordinates to image boundaries.
     *
     * @param ImageInterface $image       The source image.
     * @param int[]          $coordinates The coordinates.
     *
     * @return int[] The limited coordinates.
     */
    public function limitToImageBoundaries(ImageInterface $image, array $coordinates): array
    {
        if ($coordinates[0] > ($image->width() - $coordinates[2])) {
            $coordinates[0] = $image->width() - $coordinates[2];
        }

        if ($coordinates[1] > ($image->height() - $coordinates[3])) {
            $coordinates[1] = $image->height() - $coordinates[3];
        }

        return $coordinates;
    }
}
