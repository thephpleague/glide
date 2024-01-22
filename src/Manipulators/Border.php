<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\Helpers\Color;
use League\Glide\Manipulators\Helpers\Dimension;

/**
 * @property string $border
 * @property string $dpr
 */
class Border extends BaseManipulator
{
    /**
     * Perform border image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        if ($border = $this->getBorder($image)) {
            list($width, $color, $method) = $border;

            if ('overlay' === $method) {
                return $this->runOverlay($image, $width, $color);
            }

            if ('shrink' === $method) {
                return $this->runShrink($image, $width, $color);
            }

            if ('expand' === $method) {
                return $this->runExpand($image, $width, $color);
            }
        }

        return $image;
    }

    /**
     * Resolve border amount.
     *
     * @param ImageInterface $image The source image.
     *
     * @return (float|string)[]|null The resolved border amount.
     *
     * @psalm-return array{0: float, 1: string, 2: string}|null
     */
    public function getBorder(ImageInterface $image): ?array
    {
        if (!$this->border) {
            return null;
        }

        $values = explode(',', $this->border);

        $width = $this->getWidth($image, $this->getDpr(), isset($values[0]) ? $values[0] : null);
        $color = $this->getColor(isset($values[1]) ? $values[1] : null);
        $method = $this->getMethod(isset($values[2]) ? $values[2] : null);

        if ($width) {
            return [$width, $color, $method];
        }

        return null;
    }

    /**
     * Get border width.
     *
     * @param ImageInterface  $image The source image.
     * @param float  $dpr   The device pixel ratio.
     * @param string $width The border width.
     *
     * @return float|null The resolved border width.
     */
    public function getWidth(ImageInterface $image, $dpr, $width): ?float
    {
        return (new Dimension($image, $dpr))->get($width);
    }

    /**
     * Get formatted color.
     *
     * @param string $color The color.
     *
     * @return string The formatted color.
     */
    public function getColor($color)
    {
        return (new Color($color))->formatted();
    }

    /**
     * Resolve the border method.
     *
     * @param string $method The raw border method.
     *
     * @return string The resolved border method.
     */
    public function getMethod($method)
    {
        if (!in_array($method, ['expand', 'shrink', 'overlay'], true)) {
            return 'overlay';
        }

        return $method;
    }

    /**
     * Resolve the device pixel ratio.
     *
     * @return float The device pixel ratio.
     */
    public function getDpr()
    {
        if (!is_numeric($this->dpr)) {
            return 1.0;
        }

        if ($this->dpr < 0 or $this->dpr > 8) {
            return 1.0;
        }

        return (float) $this->dpr;
    }

    /**
     * Run the overlay border method.
     *
     * @param ImageInterface  $image The source image.
     * @param float  $width The border width.
     * @param string $color The border color.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runOverlay(ImageInterface $image, $width, $color): ImageInterface
    {
        return $image->rectangle(
            (int) round($width / 2),
            (int) round($width / 2),
            (int) round($image->width() - ($width / 2)),
            (int) round($image->height() - ($width / 2)),
            function ($draw) use ($width, $color) {
                $draw->border($width, $color);
            }
        );
    }

    /**
     * Run the shrink border method.
     *
     * @param ImageInterface  $image The source image.
     * @param float  $width The border width.
     * @param string $color The border color.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runShrink(ImageInterface $image, $width, $color): ImageInterface
    {
        return $image
            ->resize(
                (int) round($image->width() - ($width * 2)),
                (int) round($image->height() - ($width * 2))
            )
            ->resizeCanvas(
                (int) round($width * 2),
                (int) round($width * 2),
                'center',
                true,
                $color
            );
    }

    /**
     * Run the expand border method.
     *
     * @param ImageInterface  $image The source image.
     * @param float  $width The border width.
     * @param string $color The border color.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runExpand(ImageInterface $image, $width, $color): ImageInterface
    {
        return $image->resizeCanvas(
            (int) round($width * 2),
            (int) round($width * 2),
            'center',
            true,
            $color
        );
    }
}
