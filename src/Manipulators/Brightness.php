<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Brightness implements ManipulatorInterface
{
    /**
     * Perform brightness image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $brightness = $this->getBrightness($params);

        if ($brightness) {
            $image->brightness($brightness);
        }

        return $image;
    }

    /**
     * Resolve brightness amount.
     * @param  array  $params The manipulation params.
     * @return string The resolved brightness amount.
     */
    public function getBrightness($params)
    {
        if (!isset($params['bri'])) {
            return;
        }

        if (!preg_match('/^-*[0-9]+$/', $params['bri'])) {
            return;
        }

        if ($params['bri'] < -100 or $params['bri'] > 100) {
            return;
        }

        return (int) $params['bri'];
    }
}
