<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Rectangle implements Manipulator
{
    /**
     * Perform rectangle image manipulation.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
    public function run(Request $request, Image $image)
    {
        $coordinates = $this->getCoordinates($image, $request->getParam('rect'));

        if ($coordinates) {
            $image->crop(
                (int) $coordinates[0],
                (int) $coordinates[1],
                (int) $coordinates[2],
                (int) $coordinates[3]
            );
        }
    }

    /**
     * Resolve coordinates.
     * @param  Image  $image     The source image.
     * @param  string $rectangle The rectangle.
     * @return Array  The resolved coordinates.
     */
    public function getCoordinates(Image $image, $rectangle)
    {
        $coordinates = explode(',', $rectangle);

        if (!$this->validateCoordinates($image, $coordinates)) {
            return false;
        }

        return $coordinates;
    }

    /**
     * Validate coordinates.
     * @param  Image $image       The source image.
     * @param  Array $coordinates The coordinates.
     * @return bool  Whether or not the coordinates are valid.
     */
    public function validateCoordinates(Image $image, Array $coordinates)
    {
        if (count($coordinates) !== 4) {
            return false;
        }

        foreach ($coordinates as $key => $value) {
            if (!ctype_digit($value)) {
                return false;
            }

            if (in_array($key, [0, 2]) and $value > $image->width()) {
                return false;
            }

            if (in_array($key, [1, 3]) and $value > $image->height()) {
                return false;
            }
        }

        return true;
    }
}
