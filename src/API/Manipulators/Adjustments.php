<?php

namespace Glide\API\Manipulators;

use Glide\Exceptions\ParameterException;
use Intervention\Image\Image;

class Adjustments implements ManipulatorInterface
{
    private $brightness;
    private $contrast;
    private $gamma;
    private $sharpen;

    public function setBrightness($brightness)
    {
        if (!preg_match('/^-*[0-9]+$/', $brightness)) {
            throw new ParameterException('Brightness must be a valid number.');
        }

        if ($brightness < -100 or $brightness > 100) {
            throw new ParameterException('Brightness must be between -100 and 100.');
        }

        $this->brightness = $brightness;
    }

    public function setContrast($contrast)
    {
        if (!preg_match('/^-*[0-9]+$/', $contrast)) {
            throw new ParameterException('Contrast must be a valid number.');
        }

        if ($contrast < -100 or $contrast > 100) {
            throw new ParameterException('Contrast must be between -100 and 100.');
        }

        $this->contrast = $contrast;
    }

    public function setGamma($gamma)
    {
        if (!preg_match('/^-*[0-9]+$/', $gamma)) {
            throw new ParameterException('Gamma must be a valid number.');
        }

        $this->gamma = $gamma;
    }

    public function setSharpen($sharpen)
    {
        if (!ctype_digit($sharpen)) {
            throw new ParameterException('Sharpen must be a valid number.');
        }

        if ($sharpen < 0 or $sharpen > 100) {
            throw new ParameterException('Sharpen must be between 0 and 100.');
        }

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
