<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Size implements ManipulatorInterface
{
    /**
     * Maximum image size in pixels.
     * @var int|null
     */
    protected $maxImageSize;

    /**
     * Create Size instance.
     * @param int|null $maxImageSize Maximum image size in pixels.
     */
    public function __construct($maxImageSize = null)
    {
        $this->maxImageSize = $maxImageSize;
    }

    /**
     * Set the maximum image size.
     * @param int|null Maximum image size in pixels.
     */
    public function setMaxImageSize($maxImageSize)
    {
        $this->maxImageSize = $maxImageSize;
    }

    /**
     * Get the maximum image size.
     * @return int|null Maximum image size in pixels.
     */
    public function getMaxImageSize()
    {
        return $this->maxImageSize;
    }

    /**
     * Perform size image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $width = $this->getWidth($request->get('w'));
        $height = $this->getHeight($request->get('h'));
        $fit = $this->getFit($request->get('fit'));
        $crop = $this->getCrop($request->get('crop'));

        list($width, $height) = $this->resolveMissingDimensions($image, $width, $height);
        list($width, $height) = $this->limitImageSize($width, $height);

        if (round($width) !== round($image->width()) or
            round($height) !== round($image->height())) {
            $image = $this->runResize($image, $fit, round($width), round($height), $crop);
        }

        return $image;
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

        return (double) $width;
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

        return (double) $height;
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

        if (!in_array($fit, ['contain', 'max', 'stretch', 'crop'], true)) {
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

        if (!in_array($crop, ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'], true)) {
            return 'center';
        }

        return $crop;
    }

    /**
     * Resolve missing image dimensions.
     * @param  Image        $image  The source image.
     * @param  double|false $width  The image width.
     * @param  double|false $height The image height.
     * @return double[]     The resolved width and height.
     */
    public function resolveMissingDimensions(Image $image, $width, $height)
    {
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

        return [
            (double) $width,
            (double) $height,
        ];
    }

    /**
     * Limit image size to maximum allowed image size.
     * @param  double   $width  The image width.
     * @param  double   $height The image height.
     * @return double[] The limited width and height.
     */
    public function limitImageSize($width, $height)
    {
        if ($this->maxImageSize) {
            $imageSize = $width * $height;

            if ($imageSize > $this->maxImageSize) {
                $width = $width / sqrt($imageSize / $this->maxImageSize);
                $height = $height / sqrt($imageSize / $this->maxImageSize);
            }
        }

        return [
            (double) $width,
            (double) $height,
        ];
    }

    /**
     * Perform resize image manipulation.
     * @param  Image       $image  The source image.
     * @param  string      $fit    The fit.
     * @param  string      $width  The width.
     * @param  string      $height The height.
     * @param  string|null $crop   The crop.
     * @return Image       The manipulated image.
     */
    public function runResize(Image $image, $fit, $width, $height, $crop = null)
    {
        if ($fit === 'contain') {
            return $this->runContainResize($image, $width, $height);
        }

        if ($fit === 'max') {
            return $this->runMaxResize($image, $width, $height);
        }

        if ($fit === 'stretch') {
            return $this->runStretchResize($image, $width, $height);
        }

        if ($fit === 'crop') {
            return $this->runCropResize($image, $width, $height, $crop);
        }

        return $image;
    }

    /**
     * Perform contain resize image manipulation.
     * @param  Image  $image  The source image.
     * @param  string $width  The width.
     * @param  string $height The height.
     * @return Image  The manipulated image.
     */
    public function runContainResize(Image $image, $width, $height)
    {
        return $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    /**
     * Perform max resize image manipulation.
     * @param  Image  $image  The source image.
     * @param  string $width  The width.
     * @param  string $height The height.
     * @return Image  The manipulated image.
     */
    public function runMaxResize(Image $image, $width, $height)
    {
        return $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    /**
     * Perform stretch resize image manipulation.
     * @param  Image  $image  The source image.
     * @param  string $width  The width.
     * @param  string $height The height.
     * @return Image  The manipulated image.
     */
    public function runStretchResize(Image $image, $width, $height)
    {
        return $image->resize($width, $height);
    }

    /**
     * Perform crop resize image manipulation.
     * @param  Image  $image  The source image.
     * @param  string $width  The width.
     * @param  string $height The height.
     * @param  string $crop   The crop.
     * @return Image  The manipulated image.
     */
    public function runCropResize(Image $image, $width, $height, $crop)
    {
        return $image->fit(
            $width,
            $height,
            function () {
            },
            $crop
        );
    }
}
