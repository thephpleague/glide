<?php

namespace Glide\Manipulators;

use Intervention\Image\Image;

class Size implements Manipulator
{
    private $width;
    private $height;
    private $fit = 'clip';
    private $cropPosition = 'center';
    private $cropRectangle;
    private $orientation;

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

    public function setCropPosition($cropPosition)
    {
        $this->cropPosition = $cropPosition;
    }

    public function setCropRectangle($cropRectangle)
    {
        $coordinates = explode(',', $cropRectangle);

        $this->cropRectangle['width'] = $coordinates[0];
        $this->cropRectangle['height'] = $coordinates[1];
        $this->cropRectangle['x'] = $coordinates[2];
        $this->cropRectangle['y'] = $coordinates[3];
    }

    public function setOrientation($orientation)
    {
        $parts = explode(',', $orientation);
        $this->orientation['angle'] = $parts[0];
        $this->orientation['color'] = isset($parts[1]) ? '#' . $parts[1] : '#000000';
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

        if ($this->cropRectangle) {
            $image->crop(
                $this->cropRectangle['width'],
                $this->cropRectangle['height'],
                $this->cropRectangle['x'],
                $this->cropRectangle['y']
            );
        }

        if ($this->width or $this->height) {
            if ($this->fit === 'clip') {
                $image->resize($this->width, $this->height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else if ($this->fit === 'scale') {
                $image->resize($this->width, $this->height);
            } else if ($this->fit === 'crop') {
                $image->fit($this->width, $this->height, null, $this->cropPosition);
            }
        }

        return $image;
    }
}
