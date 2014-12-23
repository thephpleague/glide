<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

class Size implements Manipulator
{
    private $orientation;
    private $width;
    private $height;
    private $fit = 'clip';
    private $rect; // crop to specific dimensions
    private $crop; // crop position when set to fit = crop

    public function setOrientation($orientation)
    {
        $parts = explode(',', $orientation);
        $this->orientation['angle'] = $parts[0];
        $this->orientation['color'] = isset($parts[1]) ? '#' . $parts[1] : '#000000';
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function setFit($fit)
    {
        $this->fit = $fit;
    }

    public function run(Image $image)
    {
        if (is_array($this->orientation)) {
            $image->rotate(
                $this->orientation['angle'],
                $this->orientation['color']
            );
        } else {
            $image->orientate();
        }

        if ($this->width or $this->height) {
            if ($this->fit === 'clip') {
                $image->resize($this->width, $this->height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else if ($this->fit === 'scale') {
                $image->resize($this->width, $this->height);
            } else if ($this->fit === 'crop') {
                $image->fit($this->width, $this->height);
            }
        }

        return $image;
    }
}
