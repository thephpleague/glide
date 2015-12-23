<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $dpr
 * @property string $fit
 * @property string $h
 * @property string $w
 */
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
        $dpr = $this->getDpr();

        list($width, $height) = $this->resolveMissingDimensions($image, $width, $height);
        list($width, $height) = $this->applyDpr($width, $height, $dpr);
        list($width, $height) = $this->limitImageSize($width, $height);

        if (round($width) !== round($image->width()) or
            round($height) !== round($image->height())) {
            $image = $this->runResize($image, $fit, round($width), round($height));
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

        if (preg_match('/^(crop)(-top-left|-top|-top-right|-left|-center|-right|-bottom-left|-bottom|-bottom-right|-[\d]{1,3}-[\d]{1,3})*$/', $this->fit)) {
            return 'crop';
        }

        return 'contain';
    }

    /**
     * Resolve crop.
     * @return string|array The resolved crop.
     */
    public function getCrop()
    {
        $cropMethods = [
            'crop-top-left' => [0, 0],
            'crop-top' => [50, 0],
            'crop-top-right' => [100, 0],
            'crop-left' => [0, 50],
            'crop-center' => [50, 50],
            'crop-right' => [100, 50],
            'crop-bottom-left' => [0, 100],
            'crop-bottom' => [50, 100],
            'crop-bottom-right' => [100, 100],
        ];

        if (array_key_exists($this->fit, $cropMethods)) {
            return $cropMethods[$this->fit];
        }

        if (preg_match('/^crop-([\d]{1,3})-([\d]{1,3})*$/', $this->fit, $matches)) {
            if ($matches[1] > 100 or $matches[2] > 100) {
                return [50, 50];
            }

            return [(int) $matches[1], (int) $matches[2]];
        }

        return [50, 50];
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
        if ($this->maxImageSize !== null) {
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
     * @return Image  The manipulated image.
     */
    public function runCropResize(Image $image, $width, $height)
    {
        list($offset_percentage_x, $offset_percentage_y) = $this->getCrop();

        $resize_width = $width;
        $resize_height = $width * ($image->height() / $image->width());

        if ($height > $resize_height) {
            $resize_width = $height * ($image->width() / $image->height());
            $resize_height = $height;
        }

        $image->resize($resize_width, $resize_height, function ($constraint) {
            $constraint->aspectRatio();
        });

        $offset_x = round(($image->width() * $offset_percentage_x / 100) - ($width / 2));
        $offset_y = round(($image->height() * $offset_percentage_y / 100) - ($height / 2));

        $max_offset_x = $image->width() - $width;
        $max_offset_y = $image->height() - $height;

        if ($offset_x < 0) {
            $offset_x = 0;
        }

        if ($offset_y < 0) {
            $offset_y = 0;
        }

        if ($offset_x > $max_offset_x) {
            $offset_x = $max_offset_x;
        }

        if ($offset_y > $max_offset_y) {
            $offset_y = $max_offset_y;
        }

        return $image->crop(
            $width,
            $height,
            $offset_x,
            $offset_y
        );
    }
}
