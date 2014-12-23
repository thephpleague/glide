<?php

namespace Glide\Manipulators;

use Glide\ParameterException;
use Intervention\Image\Image;

class Size implements Manipulator
{
    private $width;
    private $height;
    private $fit = 'clip';
    private $cropPosition = 'center';
    private $cropRectangle;
    private $orientation = 'auto';

    public function setWidth($width)
    {
        if (!ctype_digit($width)) {
            throw new ParameterException('The width must be a valid number.');
        }

        if ($width <= 0) {
            throw new ParameterException('The width must be greater than 0.');
        }

        $this->width = $width;
    }

    public function setHeight($height)
    {
        if (!ctype_digit($height)) {
            throw new ParameterException('Height must be a valid number.');
        }

        if ($height <= 0) {
            throw new ParameterException('Weight must be greater than 0.');
        }

        $this->height = $height;
    }

    public function setFit($fit)
    {
        if (!in_array($fit, ['clip', 'scale', 'crop'])) {
            throw new ParameterException('Fit only accepts "clip", "scale" or "crop".');
        }

        if (is_null($this->width) or is_null($this->height)) {
            throw new ParameterException('The width or height must be set when using fit.');
        }

        $this->fit = $fit;
    }

    public function setCropPosition($cropPosition)
    {
        if (!in_array($cropPosition, ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'])) {
            throw new ParameterException('The crop position parameter only accepts "top-left", "top", "top-right", "left", "center", "right", "bottom-left", "bottom" or "bottom-right".');
        }

        if ($this->fit !== 'crop') {
            throw new ParameterException('Crop position cannot be used unless fit is set to "crop".');
        }

        $this->cropPosition = $cropPosition;
    }

    public function setCropRectangle($cropRectangle)
    {
        $coordinates = explode(',', $cropRectangle);

        if (count($coordinates) !== 4) {
            throw new ParameterException('Rectangle crop requires "width", "height", "x" and "y".');
        }

        $this->cropRectangle['width'] = $coordinates[0];
        $this->cropRectangle['height'] = $coordinates[1];
        $this->cropRectangle['x'] = $coordinates[2];
        $this->cropRectangle['y'] = $coordinates[3];

        foreach ($this->cropRectangle as $name => $value) {
            if (!ctype_digit($value)) {
                throw new ParameterException('The "' . $name . '" parameter must be a valid number.');
            }
        }

        if ($this->cropRectangle['width'] <= 0) {
            throw new ParameterException('The "width" parameter must be greater than 0.');
        }

        if ($this->cropRectangle['height'] <= 0) {
            throw new ParameterException('The "height" parameter must be greater than 0.');
        }
    }

    public function setOrientation($orientation)
    {
        if (!in_array($orientation, ['auto', '0', '90', '180', '270'])) {
            throw new ParameterException('Orientation must be set to "auto", "0", "90", "180" or "270"');
        }

        $this->orientation = $orientation;
    }

    public function run(Image $image)
    {
        if ($this->orientation === 'auto') {
            $image->orientate();
        } else {
            $image->rotate($this->orientation);
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
