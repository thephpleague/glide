<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

class Encode implements Manipulator
{
    private $format = 'jpg';
    private $quality = 90;

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function setQuality($quality)
    {
        $this->quality = $quality;
    }

    public function run(Image $image)
    {
        return $image->encode($this->format, $this->quality)->getEncoded();
    }
}
