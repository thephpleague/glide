<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Rectangle implements ManipulatorInterface
{
    /**
     * Perform rectangle image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $coordinates = $this->getCoordinates($image, $request->get('rect'));

        if ($coordinates) {
            $coordinates = $this->limitCoordinatesToImageBoundaries($image, $coordinates);

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
     * @param  Image  $image     The source image.
     * @param  string $rectangle The rectangle.
     * @return int[]  The resolved coordinates.
     */
    public function getCoordinates(Image $image, $rectangle)
    {
        $coordinates = explode(',', $rectangle);

        if (count($coordinates) !== 4 or
            !ctype_digit($coordinates[0]) or
            !ctype_digit($coordinates[1]) or
            !ctype_digit($coordinates[2]) or
            !ctype_digit($coordinates[3]) or
            $coordinates[2] >= $image->width() or
            $coordinates[3] >= $image->height()) {
            return false;
        }

        return [
            (int) $coordinates[0],
            (int) $coordinates[1],
            (int) $coordinates[2],
            (int) $coordinates[3]
        ];
    }

    /**
     * Limit coordinates to image boundaries.
     * @param  Image $image       The source image.
     * @param  int[] $coordinates The coordinates.
     * @return int[] The limited coordinates.
     */
    public function limitCoordinatesToImageBoundaries(Image $image, array $coordinates)
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
