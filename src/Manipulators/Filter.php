<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Filter implements Manipulator
{
    public function run(Request $request, Image $image)
    {
        if ($request->filt === 'greyscale') {
            $this->runGreyscaleFilter($image);
        }

        if ($request->filt === 'sepia') {
            $this->runSepiaFilter($image);
        }
    }

    public function runGreyscaleFilter(Image $image)
    {
        $image->greyscale();
    }

    public function runSepiaFilter(Image $image)
    {
        $image->greyscale();
        $image->brightness(-10);
        $image->contrast(10);
        $image->colorize(38, 27, 12);
        $image->brightness(-10);
        $image->contrast(10);
    }
}
