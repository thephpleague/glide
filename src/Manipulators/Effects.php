<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Effects implements Manipulator
{
    public function validate(Request $request, Image $image)
    {
        return array_merge(
            $this->validateFilter($request->filt),
            $this->validateBlur($request->blur),
            $this->validatePixelate($request->pixel)
        );
    }

    public function validateFilter($filter)
    {
        if (is_null($filter)) {
            return [];
        }

        if (!in_array($filter, ['greyscale', 'sepia'])) {
            return ['filt' => 'Filter only accepts `greyscale` or `sepia`.'];
        }

        return [];
    }

    public function validateBlur($blur)
    {
        if (is_null($blur)) {
            return [];
        }

        if (!ctype_digit($blur)) {
            return ['blur' => 'Blur must be a valid number.'];
        }

        if ($blur < 0 or $blur > 100) {
            return ['blur' => 'Blur must be between `0` and `100`.'];
        }

        return [];
    }

    public function validatePixelate($pixelate)
    {
        if (is_null($pixelate)) {
            return [];
        }

        if (!ctype_digit($pixelate)) {
            return ['pixel' => 'Pixelate must be a valid number.'];
        }

        if ($pixelate < 0 or $pixelate > 1000) {
            return ['pixel' => 'Pixelate must be between `0` and `1000`.'];
        }

        return [];
    }

    public function run(Request $request, Image $image)
    {
        if ($request->filt) {
            $this->runFilter($image, $request->filt);
        }

        if ($request->blur) {
            $this->runBlur($image, $request->blur);
        }

        if ($request->pixel) {
            $this->runPixelate($image, $request->pixel);
        }
    }

    public function runFilter(Image $image, $filter)
    {
        if ($filter === 'greyscale') {
            $this->runGreyscaleFilter($image);
        }

        if ($filter === 'sepia') {
            $this->runSepiaFilter($image);
        }
    }

    public function runGreyscaleFilter(Image $image)
    {
        $image->greyscale();
    }

    public function runSepiaFilter(Image $image)
    {
        $image->greyscale()
              ->brightness(-10)
              ->contrast(10)
              ->colorize(38, 27, 12)
              ->brightness(-10)
              ->contrast(10);
    }

    public function runBlur(Image $image, $blur)
    {
        $image->blur($blur);
    }

    public function runPixelate(Image $image, $pixels)
    {
        $image->pixelate($pixels);
    }
}
