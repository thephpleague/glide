<?php

namespace Glide\API\Manipulators;

use Glide\Exceptions\ParameterException;
use Intervention\Image\Image;

class Effects implements ManipulatorInterface
{
    private $filter;
    private $blur;
    private $pixelate;

    public function setFilter($filter)
    {
        if (!in_array($filter, ['greyscale', 'sepia'])) {
            throw new ParameterException('Filter only accepts "greyscale" or "sepia".');
        }

        $this->filter = $filter;
    }

    public function setBlur($blur)
    {
        if (!ctype_digit($blur)) {
            throw new ParameterException('Blur must be a valid number.');
        }

        if ($blur < 0 or $blur > 100) {
            throw new ParameterException('Blur must be between 0 and 100.');
        }

        $this->blur = $blur;
    }

    public function setPixelate($pixelate)
    {
        if (!ctype_digit($pixelate)) {
            throw new ParameterException('Pixelate must be a valid number.');
        }

        if ($pixelate < 0 or $pixelate > 100) {
            throw new ParameterException('Pixelate must be between 0 and 1000.');
        }

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
