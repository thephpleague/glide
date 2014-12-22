<?php

namespace Glide;

use Intervention\Image\Image;

class API
{
    private $manipulators;

    public function __construct($params)
    {
        $this->manipulators = [
            'adjustments' => new Manipulators\Adjustments(),
            'size' => new Manipulators\Size(),
            'effects' => new Manipulators\Effects(),
            'encode' => new Manipulators\Encode(),
        ];

        foreach ($params as $name => $value) {
            $this->$name($value);
        }
    }

    public function bri($value)
    {
        $this->manipulators['adjustments']->setBrightness($value);
    }

    public function con($value)
    {
        $this->manipulators['adjustments']->setContrast($value);
    }

    public function gam($value)
    {
        $this->manipulators['adjustments']->setGamma($value);
    }

    public function orient($value)
    {
        $this->manipulators['size']->setOrientation($value);
    }

    public function w($value)
    {
        $this->manipulators['size']->setWidth($value);
    }

    public function h($value)
    {
        $this->manipulators['size']->setHeight($value);
    }

    public function fit($value)
    {
        $this->manipulators['size']->setFit($value);
    }

    public function blur($value)
    {
        $this->manipulators['effects']->setBlur($value);
    }

    public function fm($value)
    {
        $this->manipulators['encode']->setFormat($value);
    }

    public function q($value)
    {
        $this->manipulators['encode']->setQuality($value);
    }

    public function run(Image $image)
    {
        foreach ($this->manipulators as $manipulator) {
            $image = $manipulator->run($image);
        }

        return $image;
    }
}
