<?php

namespace Glide\Manipulators;

use Glide\Request;
use Intervention\Image\Image;

class Effects implements ManipulatorInterface
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
            return ['filt' => 'Filter only accepts "greyscale" or "sepia".'];
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
            return ['blur' => 'Blur must be between 0 and 100.'];
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
            return ['pixel' => 'Pixelate must be between 0 and 1000.'];
        }

        return [];
    }

    public function run(Request $request, Image $image)
    {
        if ($request->filt === 'greyscale') {
            $image->greyscale();
        }

        if ($request->filt === 'sepia') {
            $image->greyscale()
                  ->brightness(-10)
                  ->contrast(10)
                  ->colorize(38, 27, 12)
                  ->brightness(-10)
                  ->contrast(10);
        }

        if ($request->blur) {
            $image->blur($request->blur);
        }

        if ($request->pixel) {
            $image->pixelate($request->pixel);
        }
    }
}
