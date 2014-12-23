<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

class Effects implements Manipulator
{
    private $filter;
    private $blur;
    private $pixelate;

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function setBlur($blur)
    {
        $this->blur = $blur;
    }

    public function setPixelate($pixelate)
    {
        $this->pixelate = $pixelate;
    }

    public function run(Image $image)
    {
        if ($this->filter === 'greyscale') {
            $image->greyscale();
        }

        if ($this->filter === 'sepia') {
            $image->greyscale()
                  ->brightness(-10)
                  ->contrast(10)
                  ->colorize(38, 27, 12)
                  ->brightness(-10)
                  ->contrast(10);
        }

        if ($this->blur) {
            $image->blur($this->blur);
        }

        if ($this->pixelate) {
            $image->pixelate($this->pixelate);
        }

        return $image;
    }
}
