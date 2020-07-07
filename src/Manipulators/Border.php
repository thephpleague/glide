<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
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
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        if ($border = $this->getBorder($image)) {
            list($width, $color, $method) = $border;

            if ($method === 'overlay') {
                return $this->runOverlay($image, $width, $color);
            }

            if ($method === 'shrink') {
                return $this->runShrink($image, $width, $color);
            }

            if ($method === 'expand') {
                return $this->runExpand($image, $width, $color);
            }
        }

        return $image;
    }

    /**
     * Resolve border amount.
     * @param  Image  $image The source image.
     * @return array|null The resolved border amount.
     */
    public function getBorder(Image $image): ?array
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
     * @param  Image  $image The source image.
     * @param  double $dpr   The device pixel ratio.
     * @param  string $width The border width.
     * @return double The resolved border width.
     */
    public function getWidth(Image $image, float $dpr, string $width): float
    {
        return (float) (new Dimension($image, $dpr))->get($width);
    }

    /**
     * Get formatted color.
     * @param  string $color The color.
     * @return string The formatted color.
     */
    public function getColor(string $color): string
    {
        return (new Color($color))->formatted();
    }

    /**
     * Resolve the border method.
     * @param  string|null $method The raw border method.
     * @return string|null The resolved border method.
     */
    public function getMethod(?string $method): ?string
    {
        if (!in_array($method, ['expand', 'shrink', 'overlay'], true)) {
            return 'overlay';
        }

        return $method;
    }

    /**
     * Resolve the device pixel ratio.
     * @return double The device pixel ratio.
     */
    public function getDpr(): float
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
     * Run the overlay border method.
     * @param  Image  $image The source image.
     * @param  double $width The border width.
     * @param  string $color The border color.
     * @return Image  The manipulated image.
     */
    public function runOverlay(Image $image, float $width, string $color): Image
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
     * @param  Image  $image The source image.
     * @param  double $width The border width.
     * @param  string $color The border color.
     * @return Image  The manipulated image.
     */
    public function runShrink(Image $image, float $width, string $color): Image
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
     * @param  Image  $image The source image.
     * @param  double $width The border width.
     * @param  string $color The border color.
     * @return Image  The manipulated image.
     */
    public function runExpand(Image $image, float $width, string $color): Image
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
