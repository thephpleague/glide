<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

class Adjustments implements Manipulator
{
    private $brightness;
    private $contrast;
    private $gamma;
    private $colorize;

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

    public function setColor($color)
    {
        $colors = explode(',', $color);

        $this->colorize['r'] = (int) $colors[0];
        $this->colorize['g'] = (int) $colors[1];
        $this->colorize['b'] = (int) $colors[2];
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

        return $image;
    }
}
