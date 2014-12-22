<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

class Effects implements Manipulator
{
    private $blur;

    public function setBlur($blur)
    {
        $this->blur = $blur;
    }

    public function run(Image $image)
    {
        if ($this->blur) {
            $image->blur($this->blur);
        }

        return $image;
    }
}
