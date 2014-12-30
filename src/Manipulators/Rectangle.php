<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Rectangle implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $coordinates = $this->getCoordinates($image, $request->rect);

        if ($coordinates) {
            $image->crop(
                (int) $coordinates['width'],
                (int) $coordinates['height'],
                (int) $coordinates['x'],
                (int) $coordinates['y']
            );
        }
    }

    public function getCoordinates(Image $image, $rectangle)
    {
        $coordinates = explode(',', $rectangle);

        if (count($coordinates) !== 4) {
            return false;
        }

        $coordinates = [
            'width' => $coordinates[0],
            'height' => $coordinates[1],
            'x' => $coordinates[2],
            'y' => $coordinates[3],
        ];

        foreach ($coordinates as $name => $value) {
            if (!ctype_digit($value)) {
                return false;
            }

            if (in_array($name, ['width', 'height']) and $value <= 0) {
                return false;
            }

            if (in_array($name, ['width', 'x']) and $value > $image->width()) {
                return false;
            }

            if (in_array($name, ['height', 'y']) and $value > $image->height()) {
                return false;
            }
        }

        return $coordinates;
    }
}
