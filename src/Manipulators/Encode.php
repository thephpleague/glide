<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Encode implements ManipulatorInterface
{
    /**
     * Perform output image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $format = $this->getFormat($image, $params);
        $quality = $this->getQuality($params);

        if ($format === 'pjpg') {
            $image->interlace();
            $format = 'jpg';
        }

        return $image->encode($format, $quality);
    }

    /**
     * Resolve format.
     * @param  Image  $image  The source image.
     * @param  array  $params The manipulation params.
     * @return string The resolved format.
     */
    public function getFormat(Image $image, $params)
    {
        $format = isset($params['fm']) ? $params['fm'] : null;

        $allowed = [
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'pjpg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        if (array_key_exists($format, $allowed)) {
            return $format;
        }

        if ($format = array_search($image->mime(), $allowed, true)) {
            return $format;
        }

        return 'jpg';
    }

    /**
     * Resolve quality.
     * @param  array  $params The manipulation params.
     * @return string The resolved quality.
     */
    public function getQuality($params)
    {
        $default = 90;

        if (!isset($params['q'])) {
            return $default;
        }

        if (!is_numeric($params['q'])) {
            return $default;
        }

        if ($params['q'] < 0 or $params['q'] > 100) {
            return $default;
        }

        return (int) $params['q'];
    }
}
