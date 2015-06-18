<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Gamma implements ManipulatorInterface
{
    /**
     * Perform gamma image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $gamma = $this->getGamma($params);

        if ($gamma) {
            $image->gamma($gamma);
        }

        return $image;
    }

    /**
     * Resolve gamma amount.
     * @param  array  $params The manipulation params.
     * @return string The resolved gamma amount.
     */
    public function getGamma($params)
    {
        if (!isset($params['gam'])) {
            return;
        }

        if (!preg_match('/^[0-9]\.*[0-9]*$/', $params['gam'])) {
            return;
        }

        if ($params['gam'] < 0.1 or $params['gam'] > 9.99) {
            return;
        }

        return (double) $params['gam'];
    }
}
