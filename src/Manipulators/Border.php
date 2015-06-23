<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Manipulators\Helpers\Color;
use League\Glide\Manipulators\Helpers\Dimension;

class Border implements ManipulatorInterface
{
    /**
     * Perform border image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        if ($border = $this->getBorder($image, $params)) {
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
     * @param  array  $params The manipulation params.
     * @return string The resolved border amount.
     */
    public function getBorder(Image $image, $params)
    {
        if (!isset($params['border'])) {
            return;
        }

        $values = explode(',', $params['border']);
        $dpr = $this->getDpr($params);

        $width = $this->getWidth($image, $dpr, isset($values[0]) ? $values[0] : null);
        $color = $this->getColor(isset($values[1]) ? $values[1] : null);
        $method = $this->getMethod(isset($values[2]) ? $values[2] : null);

        if ($width) {
            return [$width, $color, $method];
        }
    }

    /**
     * Get a dimension.
     * @param  Image       $image  The source image.
     * @param  array       $params The manipulation params.
     * @param  string      $field  The requested field.
     * @return double|null The dimension.
     */
    public function getWidth(Image $image, $dpr, $width)
    {
        return (new Dimension($image, $dpr))->get($width);
    }

    /**
     * Resolve the device pixel ratio.
     * @param  array  $params The manipulation params.
     * @return double The device pixel ratio.
     */
    public function getDpr($params)
    {
        if (!isset($params['dpr'])) {
            return 1.0;
        }

        if (!is_numeric($params['dpr'])) {
            return 1.0;
        }

        if ($params['dpr'] < 0 or $params['dpr'] > 8) {
            return 1.0;
        }

        return (double) $params['dpr'];
    }

    /**
     * Get formatted color.
     * @param  string $color The color.
     * @return string The formatted color.
     */
    public function getColor($color)
    {
        return (new Color($color))->formatted();
    }

    /**
     * Resolve the border method.
     * @param  string $method The raw border method.
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
     * Run the overlay border method.
     * @param  Image  $image The source image.
     * @param  double $width The border width.
     * @param  string $color The border color.
     * @return Image  The manipulated image.
     */
    public function runOverlay(Image $image, $width, $color)
    {
        return $image->rectangle(
            $width / 2,
            $width / 2,
            $image->width() - ($width / 2),
            $image->height() - ($width / 2),
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
    public function runShrink(Image $image, $width, $color)
    {
        return $image
            ->resize(
                $image->width() - ($width * 2),
                $image->height() - ($width * 2)
            )
            ->resizeCanvas(
                $width * 2,
                $width * 2,
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
    public function runExpand(Image $image, $width, $color)
    {
        return $image->resizeCanvas(
            $width * 2,
            $width * 2,
            'center',
            true,
            $color
        );
    }
}
