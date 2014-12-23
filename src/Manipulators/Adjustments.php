<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

class Adjustments implements Manipulator
{
    private $brightness;
    private $contrast;
    private $gamma;
    private $sharpen;

    public function setBrightness($brightness)
    {
        $this->brightness = $brightness;
    }

    public function setContrast($contrast)
    {
        $this->contrast = $contrast;
    }

    public function setGamma($gamma)
    {
        $this->gamma = $gamma;
    }

    public function setSharpen($sharpen)
    {
        $this->sharpen = $sharpen;
    }

    public function run(Image $image)
    {
        if ($this->brightness) {
            $image->brightness($this->brightness);
        }

        if ($this->contrast) {
            $image->contrast($this->contrast);
        }

        if ($this->gamma) {
            $image->gamma($this->gamma);
        }

        if ($this->sharpen) {
            $image->sharpen($this->sharpen);
        }

        return $image;
    }
}
