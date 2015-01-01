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

    /**
     * Perform size image manipulation.
     * @param Request $request The request object.
     * @param Image   $image   The source image.
     */
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

    /**
     * Resolve width.
     * @param  string $width The width.
     * @return string The resolved width.
     */
    public function getWidth($width)
    {
        if (is_null($width)) {
            return false;
        }

        if (!ctype_digit($width)) {
            return false;
        }

        return $width;
    }

    /**
     * Resolve height.
     * @param  string $height The height.
     * @return string The resolved height.
     */
    public function getHeight($height)
    {
        if (is_null($height)) {
            return false;
        }

        if (!ctype_digit($height)) {
            return false;
        }

        return $height;
    }

    /**
     * Resolve fit.
     * @param  string $fit The fit.
     * @return string The resolved fit.
     */
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

    /**
     * Resolve crop.
     * @param  string $crop The crop.
     * @return string The resolved crop.
     */
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

    /**
     * Perform resize image manipulation.
     * @param Image       $image  The source image.
     * @param string      $fit    The fit.
     * @param string      $width  The width.
     * @param string      $height The height.
     * @param string|null $crop   The crop.
     */
    public function runResize(Image $image, $fit, $width, $height, $crop = null)
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

    /**
     * Perform contain resize image manipulation.
     * @param Image  $image  The source image.
     * @param string $width  The width.
     * @param string $height The height.
     */
    public function runContainResize(Image $image, $width, $height)
    {
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    /**
     * Perform max resize image manipulation.
     * @param Image  $image  The source image.
     * @param string $width  The width.
     * @param string $height The height.
     */
    public function runMaxResize(Image $image, $width, $height)
    {
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    /**
     * Perform stretch resize image manipulation.
     * @param Image  $image  The source image.
     * @param string $width  The width.
     * @param string $height The height.
     */
    public function runStretchResize(Image $image, $width, $height)
    {
        $image->resize($width, $height);
    }

    /**
     * Perform crop resize image manipulation.
     * @param Image  $image  The source image.
     * @param string $width  The width.
     * @param string $height The height.
     * @param string $crop   The crop.
     */
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
