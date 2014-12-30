<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Output implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        $image->encode(
            $this->getFormat($request->fm),
            $this->getQuality($request->q)
        );
    }

    public function getFormat($format)
    {
        $default = 'jpg';

        if (is_null($format)) {
            return $default;
        }

        if (!in_array($format, ['jpg', 'png', 'gif'])) {
            return $default;
        }

        return $format;
    }

    public function getQuality($quality)
    {
        $default = 90;

        if (is_null($quality)) {
            return $default;
        }

        if (!ctype_digit($quality)) {
            return $default;
        }

        if ($quality < 0 or $quality > 100) {
            return $default;
        }

        return $quality;
    }
}
