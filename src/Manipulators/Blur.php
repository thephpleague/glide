<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Blur implements ManipulatorInterface
{
    /**
     * Perform blur image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $blur = $this->getBlur($params);

        if ($blur) {
            $image->blur($blur);
        }

        return $image;
    }

    /**
     * Resolve blur amount.
     * @param  array  $params The manipulation params.
     * @return string The resolved blur amount.
     */
    public function getBlur($params)
    {
        if (!isset($params['blur'])) {
            return;
        }

        if (!is_numeric($params['blur'])) {
            return;
        }

        if ($params['blur'] < 0 or $params['blur'] > 100) {
            return;
        }

        return (int) $params['blur'];
    }
}
