<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Geometry\Rectangle;
use Intervention\Image\Interfaces\ImageInterface;

class Size extends BaseManipulator
{
    /**
     * Maximum image size in pixels.
     */
    protected ?int $maxImageSize = null;

    /**
     * Create Size instance.
     *
     * @param int|null $maxImageSize Maximum image size in pixels.
     */
    public function __construct(?int $maxImageSize = null)
    {
        $this->maxImageSize = $maxImageSize;
    }

    /**
     * Set the maximum image size.
     *
     * @param int|null Maximum image size in pixels.
     */
    public function setMaxImageSize(?int $maxImageSize = null): void
    {
        $this->maxImageSize = $maxImageSize;
    }

    /**
     * Get the maximum image size.
     *
     * @return int|null Maximum image size in pixels.
     */
    public function getMaxImageSize(): ?int
    {
        return $this->maxImageSize;
    }

    /**
     * Perform size image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $fit = $this->getFit();
        $dpr = $this->getDpr();

        [$width, $height] = $this->resolveMissingDimensions($image, $width, $height);
        [$width, $height] = $this->applyDpr($width, $height, $dpr);
        [$width, $height] = $this->limitImageSize($width, $height);

        if ($width !== $image->width() || $height !== $image->height() || 1.0 !== $this->getCrop()[2]) {
            $image = $this->runResize($image, $fit, $width, $height);
        }

        return $image;
    }

    /**
     * Resolve width.
     *
     * @return int|null The resolved width.
     */
    public function getWidth(): ?int
    {
        $w = (int) $this->getParam('w');

        return $w <= 0 ? null : $w;
    }

    /**
     * Resolve height.
     *
     * @return int|null The resolved height.
     */
    public function getHeight(): ?int
    {
        $h = (int) $this->getParam('h');

        return $h <= 0 ? null : $h;
    }

    /**
     * Resolve fit.
     *
     * @return string The resolved fit.
     */
    public function getFit(): string
    {
        $fit = (string) $this->getParam('fit');

        if (in_array($fit, ['contain', 'fill', 'max', 'stretch', 'fill-max'], true)) {
            return $fit;
        }

        if (preg_match('/^(crop)(-top-left|-top|-top-right|-left|-center|-right|-bottom-left|-bottom|-bottom-right|-[\d]{1,3}-[\d]{1,3}(?:-[\d]{1,3}(?:\.\d+)?)?)*$/', $fit)) {
            return 'crop';
        }

        return 'contain';
    }

    /**
     * Resolve the device pixel ratio.
     *
     * @return float The device pixel ratio.
     */
    public function getDpr(): float
    {
        $dpr = $this->getParam('dpr');

        if (!is_numeric($dpr)) {
            return 1.0;
        }

        if ($dpr < 0 || $dpr > 8) {
            return 1.0;
        }

        return (float) $dpr;
    }

    /**
     * Resolve missing image dimensions.
     *
     * @param ImageInterface $image  The source image.
     * @param int|null       $width  The image width.
     * @param int|null       $height The image height.
     *
     * @return int[] The resolved width and height.
     */
    public function resolveMissingDimensions(ImageInterface $image, ?int $width = null, ?int $height = null): array
    {
        if (is_null($width) and is_null($height)) {
            $width = $image->width();
            $height = $image->height();
        }

        if (is_null($width) || is_null($height)) {
            $size = (new Rectangle($image->width(), $image->height()))
                ->scale($width, $height);

            $width = $size->width();
            $height = $size->height();
        }

        return [
            $width,
            $height,
        ];
    }

    /**
     * Apply the device pixel ratio.
     *
     * @param int   $width  The target image width.
     * @param int   $height The target image height.
     * @param float $dpr    The device pixel ratio.
     *
     * @return int[] The modified width and height.
     */
    public function applyDpr(int $width, int $height, float $dpr)
    {
        $width = $width * $dpr;
        $height = $height * $dpr;

        return [
            (int) round($width),
            (int) round($height),
        ];
    }

    /**
     * Limit image size to maximum allowed image size.
     *
     * @param int $width  The image width.
     * @param int $height The image height.
     *
     * @return int[] The limited width and height.
     */
    public function limitImageSize(int $width, int $height): array
    {
        if (null !== $this->maxImageSize) {
            $imageSize = $width * $height;

            if ($imageSize > $this->maxImageSize) {
                $width = $width / sqrt($imageSize / $this->maxImageSize);
                $height = $height / sqrt($imageSize / $this->maxImageSize);
            }
        }

        return [
            (int) $width,
            (int) $height,
        ];
    }

    /**
     * Perform resize image manipulation.
     *
     * @param ImageInterface $image  The source image.
     * @param string         $fit    The fit.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runResize(ImageInterface $image, string $fit, int $width, int $height): ImageInterface
    {
        if ('contain' === $fit) {
            return $this->runContainResize($image, $width, $height);
        }

        if ('fill' === $fit) {
            return $this->runFillResize($image, $width, $height);
        }

        if ('fill-max' === $fit) {
            return $this->runFillMaxResize($image, $width, $height);
        }

        if ('max' === $fit) {
            return $this->runMaxResize($image, $width, $height);
        }

        if ('stretch' === $fit) {
            return $this->runStretchResize($image, $width, $height);
        }

        if ('crop' === $fit) {
            return $this->runCropResize($image, $width, $height);
        }

        return $image;
    }

    /**
     * Perform contain resize image manipulation.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runContainResize(ImageInterface $image, int $width, int $height): ImageInterface
    {
        return $image->scale($width, $height);
    }

    /**
     * Perform max resize image manipulation.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runMaxResize(ImageInterface $image, int $width, int $height): ImageInterface
    {
        return $image->scaleDown($width, $height);
    }

    /**
     * Perform fill resize image manipulation.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runFillResize(ImageInterface $image, int $width, int $height): ImageInterface
    {
        return $image->pad($width, $height);
    }

    /**
     * Perform fill-max resize image manipulation.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runFillMaxResize(ImageInterface $image, int $width, int $height): ImageInterface
    {
        return $image->contain($width, $height);
    }

    /**
     * Perform stretch resize image manipulation.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runStretchResize(ImageInterface $image, int $width, int $height): ImageInterface
    {
        return $image->resize($width, $height);
    }

    /**
     * Perform crop resize image manipulation.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runCropResize(ImageInterface $image, int $width, int $height): ImageInterface
    {
        [$resize_width, $resize_height] = $this->resolveCropResizeDimensions($image, $width, $height);

        $zoom = $this->getCrop()[2];

        $image->scale((int) round($resize_width * $zoom), (int) round($resize_height * $zoom));

        [$offset_x, $offset_y] = $this->resolveCropOffset($image, $width, $height);

        return $image->crop($width, $height, $offset_x, $offset_y);
    }

    /**
     * Resolve the crop resize dimensions.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return array The resize dimensions.
     */
    public function resolveCropResizeDimensions(ImageInterface $image, int $width, int $height): array
    {
        if ($height > $width * ($image->height() / $image->width())) {
            return [$height * ($image->width() / $image->height()), $height];
        }

        return [$width, $width * ($image->height() / $image->width())];
    }

    /**
     * Resolve the crop offset.
     *
     * @param ImageInterface $image  The source image.
     * @param int            $width  The width.
     * @param int            $height The height.
     *
     * @return array The crop offset.
     */
    public function resolveCropOffset(ImageInterface $image, int $width, int $height): array
    {
        [$offset_percentage_x, $offset_percentage_y] = $this->getCrop();

        $offset_x = (int) (($image->width() * $offset_percentage_x / 100) - ($width / 2));
        $offset_y = (int) (($image->height() * $offset_percentage_y / 100) - ($height / 2));

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

        return [$offset_x, $offset_y];
    }

    /**
     * Resolve crop with zoom.
     *
     * @return (float|int)[] The resolved crop.
     *
     * @psalm-return array{0: int, 1: int, 2: float}
     */
    public function getCrop(): array
    {
        $cropMethods = [
            'crop-top-left' => [0, 0, 1.0],
            'crop-top' => [50, 0, 1.0],
            'crop-top-right' => [100, 0, 1.0],
            'crop-left' => [0, 50, 1.0],
            'crop-center' => [50, 50, 1.0],
            'crop-right' => [100, 50, 1.0],
            'crop-bottom-left' => [0, 100, 1.0],
            'crop-bottom' => [50, 100, 1.0],
            'crop-bottom-right' => [100, 100, 1.0],
        ];

        $fit = (string) $this->getParam('fit');

        if ('' === $fit) {
            return [50, 50, 1.0];
        }

        if (array_key_exists($fit, $cropMethods)) {
            return $cropMethods[$fit];
        }

        if (preg_match('/^crop-([\d]{1,3})-([\d]{1,3})(?:-([\d]{1,3}(?:\.\d+)?))*$/', $fit, $matches)) {
            $matches[3] = isset($matches[3]) ? $matches[3] : 1;

            if ($matches[1] > 100 || $matches[2] > 100 || $matches[3] > 100) {
                return [50, 50, 1.0];
            }

            return [
                (int) $matches[1],
                (int) $matches[2],
                (float) $matches[3],
            ];
        }

        return [50, 50, 1.0];
    }
}
