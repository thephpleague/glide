<?php

namespace League\Glide\Api\Manipulator;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Request;

class Circle implements ManipulatorInterface
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
     * Perform circle image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        if ($request->get('circle')) {
            $width  = $this->getWidth($request->get('w'));
            $height = $this->getHeight($request->get('h'));

            list($width, $height) = $this->resolveMissingDimensions($image, $width, $height);
            list($width, $height) = $this->limitImageSize($width, $height);

            $manager = new ImageManager(['driver' => 'imagick']);

            $circleMask = $manager->canvas($width, $height);
            $circleMask = $circleMask->ellipse($width, $height, ($width * 0.5), ($height * 0.5), function ($draw) {
                $draw->background('#fff');
            });

            $image->mask($circleMask, true);
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
     * Resolve missing image dimensions.
     * @param  Image        $image  The source image.
     * @param  double|false $width  The image width.
     * @param  double|false $height The image height.
     * @return double[]     The resolved width and height.
     */
    public function resolveMissingDimensions(Image $image, $width, $height)
    {
        if (!$width and !$height) {
            $width  = $image->width();
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
                $width  = $width / sqrt($imageSize / $this->maxImageSize);
                $height = $height / sqrt($imageSize / $this->maxImageSize);
            }
        }

        return [
            (double) $width,
            (double) $height,
        ];
    }
}
