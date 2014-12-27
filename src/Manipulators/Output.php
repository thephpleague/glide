<?php

namespace Glide\Manipulators;

use Glide\Request;
use Intervention\Image\Image;

class Output implements ManipulatorInterface
{
    public function validate(Request $request, Image $image)
    {
        return array_merge(
            $this->validateFormat($request->fm),
            $this->validateQuality($request->q)
        );
    }

    public function validateFormat($format)
    {
        if (is_null($format)) {
            return [];
        }

        if (!in_array($format, ['jpg', 'png', 'gif'])) {
            return ['fm' => 'Format only accepts "jpg", "png" or "gif".'];
        }

        return [];
    }

    public function validateQuality($quality)
    {
        if (is_null($quality)) {
            return [];
        }

        if (!ctype_digit($quality)) {
            return ['q' => 'Quality must be a valid number.'];
        }

        if ($quality < 0 or $quality > 100) {
            return ['q' => 'Quality must be between 0 and 100.'];
        }

        return [];
    }

    public function run(Request $request, Image $image)
    {
        $image->encode(
            $request->fm ?: 'jpg',
            $request->q ?: 90
        );
    }
}
