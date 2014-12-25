<?php

namespace Glide\API\Manipulators;

use Glide\Exceptions\ParameterException;
use Intervention\Image\Image;

class Output implements ManipulatorInterface
{
    private $format = 'jpg';
    private $quality = 90;

    public function setFormat($format)
    {
        if (!in_array($format, ['jpg', 'png', 'gif'])) {
            throw new ParameterException('Format only accepts "jpg", "png" or "gif".');
        }

        $this->format = $format;
    }

    public function setQuality($quality)
    {
        if (!ctype_digit($quality)) {
            throw new ParameterException('Quality must be a valid number.');
        }

        if ($quality < 0 or $quality > 100) {
            throw new ParameterException('Quality must be between 0 and 100.');
        }

        $this->quality = $quality;
    }

    public function run(Image $image)
    {
        return $image->encode($this->format, $this->quality)->getEncoded();
    }
}
