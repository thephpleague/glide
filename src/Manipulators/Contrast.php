<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Contrast implements ManipulatorInterface
{
    /**
     * Perform contrast image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $contrast = $this->getContrast($params);

        if ($contrast) {
            $image->contrast($contrast);
        }

        return $image;
    }

    /**
     * Resolve contrast amount.
     * @param  array  $params The manipulation params.
     * @return string The resolved contrast amount.
     */
    public function getContrast($params)
    {
        if (!isset($params['con'])) {
            return;
        }

        if (!preg_match('/^-*[0-9]+$/', $params['con'])) {
            return;
        }

        if ($params['con'] < -100 or $params['con'] > 100) {
            return;
        }

        return (int) $params['con'];
    }
}
