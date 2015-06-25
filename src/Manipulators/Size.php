<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Size extends BaseManipulator
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
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image)
    {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $fit = $this->getFit();
        $crop = $this->getCrop();
        $dpr = $this->getDpr();

        list($width, $height) = $this->resolveMissingDimensions($image, $width, $height);
        list($width, $height) = $this->applyDpr($width, $height, $dpr);
        list($width, $height) = $this->limitImageSize($width, $height);

        if (round($width) !== round($image->width()) or
            round($height) !== round($image->height())) {
            $image = $this->runResize($image, $fit, round($width), round($height), $crop);
        }

        return $image;
    }

    /**
     * Resolve width.
     * @return string The resolved width.
     */
    public function getWidth()
    {
        if (!is_numeric($this->w)) {
            return;
        }

        if ($this->w <= 0) {
            return;
        }

        return (double) $this->w;
    }

    /**
     * Resolve height.
     * @return string The resolved height.
     */
    public function getHeight()
    {
        if (!is_numeric($this->h)) {
            return;
        }

        if ($this->h <= 0) {
            return;
        }

        return (double) $this->h;
    }

    /**
     * Resolve fit.
     * @return string The resolved fit.
     */
    public function getFit()
    {
        if (in_array($this->fit, ['contain', 'fill', 'max', 'stretch'], true)) {
            return $this->fit;
        }

        $cropMethods = [
            'crop',
            'crop-top-left',
            'crop-top',
            'crop-top-right',
            'crop-left',
            'crop-center',
            'crop-right',
            'crop-bottom-left',
            'crop-bottom',
            'crop-bottom-right',
        ];

        if (in_array($this->fit, $cropMethods, true)) {
            return 'crop';
        }

        return 'contain';
    }

    /**
     * Resolve crop.
     * @return string The resolved crop.
     */
    public function getCrop()
    {
        $cropMethods = [
            'crop-top-left',
            'crop-top',
            'crop-top-right',
            'crop-left',
            'crop-center',
            'crop-right',
            'crop-bottom-left',
            'crop-bottom',
            'crop-bottom-right',
        ];

        if (!in_array($this->fit, $cropMethods, true)) {
            return 'center';
        }

        return substr($this->fit, 5);
    }

    /**
     * Resolve the device pixel ratio.
     * @return double The device pixel ratio.
     */
    public function getDpr()
    {
        if (!is_numeric($this->dpr)) {
            return 1.0;
        }

        if ($this->dpr < 0 or $this->dpr > 8) {
            return 1.0;
        }

        return (double) $this->dpr;
    }

    /**
     * Resolve missing image dimensions.
     * @param  Image       $image  The source image.
     * @param  double|null $width  The image width.
     * @param  double|null $height The image height.
     * @return double[]    The resolved width and height.
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
     * Apply the device pixel ratio.
     * @param  double   $width  The target image width.
     * @param  double   $height The target image height.
     * @param  double   $dpr    The device pixel ratio.
     * @return double[] The modified width and height.
     */
    public function applyDpr($width, $height, $dpr)
    {
        $width = $width * $dpr;
        $height = $height * $dpr;

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

        if ($fit === 'fill') {
            return $this->runFillResize($image, $width, $height);
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
     * Perform fill resize image manipulation.
     * @param  Image  $image  The source image.
     * @param  string $width  The width.
     * @param  string $height The height.
     * @return Image  The manipulated image.
     */
    public function runFillResize($image, $width, $height)
    {
        $image = $this->runMaxResize($image, $width, $height);
        return $image->resizeCanvas($width, $height, 'center');
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
