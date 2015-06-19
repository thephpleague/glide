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
                $image->rectangle(
                    $width / 2,
                    $width / 2,
                    $image->width() - ($width / 2),
                    $image->height() - ($width / 2),
                    function ($draw) use ($width, $color) {
                        $draw->border($width, $color);
                    }
                );
            }

            if ($method === 'shrink') {
                $image
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

            if ($method === 'expand') {
                $image->resizeCanvas(
                    $width * 2,
                    $width * 2,
                    'center',
                    true,
                    $color
                );
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

        $width = $this->getWidth($image, isset($values[0]) ? $values[0] : null);
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
    public function getWidth(Image $image, $width)
    {
        return (new Dimension($image))->get($width);
    }

    public function getColor($color)
    {
        return (new Color($color))->formatted();
    }

    public function getMethod($method)
    {
        if (!in_array($method, ['expand', 'shrink', 'overlay'], true)) {
            return 'overlay';
        }

        return $method;
    }
}
