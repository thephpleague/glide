<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Pixelate implements ManipulatorInterface
{
    /**
     * Perform pixelate image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $pixelate = $this->getPixelate($params);

        if ($pixelate) {
            $image->pixelate($pixelate);
        }

        return $image;
    }

    /**
     * Resolve pixelate amount.
     * @param  array  $params The manipulation params.
     * @return string The resolved pixelate amount.
     */
    public function getPixelate($params)
    {
        if (!isset($params['pixel'])) {
            return;
        }

        if (!is_numeric($params['pixel'])) {
            return;
        }

        if ($params['pixel'] < 0 or $params['pixel'] > 1000) {
            return;
        }

        return (int) $params['pixel'];
    }
}
