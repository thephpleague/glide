<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\Helpers\Color;
use League\Glide\Manipulators\Helpers\Dimension;

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
        $border = $this->getBorder($image);

        if ($border) {
            [$width, $color, $method] = $border;

            return $this->{'run'.$method}($image, $width, $color);
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
        $border = (string) $this->getParam('border');
        if ('' === $border) {
            return null;
        }

        $values = explode(',', $border);

        $width = $this->getWidth($image, $this->getDpr(), $values[0]);
        $color = $this->getColor(isset($values[1]) ? $values[1] : 'ffffff');
        $method = $this->getMethod(isset($values[2]) ? $values[2] : 'overlay');

        if (null !== $width) {
            return [$width, $color, $method];
        }

        return null;
    }

    /**
     * Get border width.
     *
     * @param ImageInterface $image The source image.
     * @param float          $dpr   The device pixel ratio.
     * @param string         $width The border width.
     *
     * @return float|null The resolved border width.
     */
    public function getWidth(ImageInterface $image, float $dpr, string $width): ?float
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
    public function getColor(string $color): string
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
    public function getMethod(string $method): string
    {
        return match ($method) {
            'expand' => 'expand',
            'shrink' => 'shrink',
            default => 'overlay',
        };
    }

    /**
     * Resolve the device pixel ratio.
     *
     * @return float The device pixel ratio.
     */
    public function getDpr(): float
    {
        $dpr = $this->getParam('dpr');

        if (!is_numeric($dpr)
            || $dpr < 0
            || $dpr > 8
        ) {
            return 1.0;
        }

        return (float) $dpr;
    }

    /**
     * Run the overlay border method.
     *
     * @param ImageInterface $image The source image.
     * @param float          $width The border width.
     * @param string         $color The border color.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runOverlay(ImageInterface $image, float $width, string $color): ImageInterface
    {
        return $image->drawRectangle(
            (int) round($width / 2),
            (int) round($width / 2),
            function (RectangleFactory $rectangle) use ($image, $width, $color) {
                $rectangle->size(
                    (int) round($image->width() - $width),
                    (int) round($image->height() - $width),
                );
                $rectangle->border($color, intval($width));
            }
        );
    }

    /**
     * Run the shrink border method.
     *
     * @param ImageInterface $image The source image.
     * @param float          $width The border width.
     * @param string         $color The border color.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runShrink(ImageInterface $image, float $width, string $color): ImageInterface
    {
        return $image
            ->resize(
                (int) round($image->width() - ($width * 2)),
                (int) round($image->height() - ($width * 2))
            )
            ->resizeCanvasRelative(
                (int) round($width * 2),
                (int) round($width * 2),
                $color,
                'center',
            );
    }

    /**
     * Run the expand border method.
     *
     * @param ImageInterface $image The source image.
     * @param float          $width The border width.
     * @param string         $color The border color.
     *
     * @return ImageInterface The manipulated image.
     */
    public function runExpand(ImageInterface $image, float $width, string $color): ImageInterface
    {
        return $image->resizeCanvasRelative(
            (int) round($width * 2),
            (int) round($width * 2),
            $color,
            'center',
        );
    }
}
