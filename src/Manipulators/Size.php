<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Size implements Manipulator
{
    private $maxImageSize;

    public function __construct($maxImageSize = null)
    {
        $this->maxImageSize = $maxImageSize;
    }

    public function run(Request $request, Image $image)
    {
        $width = $this->getWidth($request->getParam('w'));
        $height = $this->getHeight($request->getParam('h'));
        $fit = $this->getFit($request->getParam('fit'));
        $crop = $this->getCrop($request->getParam('crop'));

        if (!$width and !$height) {
            $width = $image->width();
            $height = $image->height();
        }

        if (!$width) {
            $width = $height * ($image->width() / $image->height());
        }

        if (!$height) {
            $height = $width / ($image->width() / $image->height());
        }

        if ($this->maxImageSize) {
            $imageSize = $width * $height;

            if ($imageSize > $this->maxImageSize) {
                $width = $width / sqrt($imageSize / $this->maxImageSize);
                $height = $height / sqrt($imageSize / $this->maxImageSize);
            }
        }

        if (round($width) !== round($image->width()) and
            round($height) !== round($image->height())) {
            $this->runResize($image, $fit, round($width), round($height), $crop);
        }
    }

    public function getWidth($width)
    {
        if (is_null($width)) {
            return false;
        }

        if (!ctype_digit($width)) {
            return false;
        }

        if ($width <= 0) {
            return false;
        }

        return $width;
    }

    public function getHeight($height)
    {
        if (is_null($height)) {
            return false;
        }

        if (!ctype_digit($height)) {
            return false;
        }

        if ($height <= 0) {
            return false;
        }

        return $height;
    }

    public function getFit($fit)
    {
        if (is_null($fit)) {
            return 'contain';
        }

        if (!in_array($fit, ['contain', 'max', 'stretch', 'crop'])) {
            return 'contain';
        }

        return $fit;
    }

    public function getCrop($crop)
    {
        if (is_null($crop)) {
            return 'center';
        }

        if (!in_array($crop, ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'])) {
            return 'center';
        }

        return $crop;
    }

    public function runResize(Image $image, $fit, $width, $height, $crop)
    {
        if ($fit === 'contain') {
            $this->runContainResize($image, $width, $height);
        }

        if ($fit === 'max') {
            $this->runMaxResize($image, $width, $height);
        }

        if ($fit === 'stretch') {
            $this->runStretchResize($image, $width, $height);
        }

        if ($fit === 'crop') {
            $this->runCropResize($image, $width, $height, $crop);
        }
    }

    public function runContainResize(Image $image, $width, $height)
    {
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    public function runMaxResize(Image $image, $width, $height)
    {
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    public function runStretchResize(Image $image, $width, $height)
    {
        $image->resize($width, $height);
    }

    public function runCropResize(Image $image, $width, $height, $crop)
    {
        $image->fit(
            $width,
            $height,
            function () {
            },
            $crop
        );
    }
}
