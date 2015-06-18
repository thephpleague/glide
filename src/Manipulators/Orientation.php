<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Orientation implements ManipulatorInterface
{
    /**
     * Perform orientation image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $orientation = $this->getOrientation($params);

        if ($orientation === 'auto') {
            return $image->orientate();
        }

        return $image->rotate($orientation);
    }

    /**
     * Resolve orientation.
     * @param  array  $params The manipulation params.
     * @return string The resolved orientation.
     */
    public function getOrientation($params)
    {
        if (!isset($params['or'])) {
            return 'auto';
        }

        if (!in_array($params['or'], ['auto', '0', '90', '180', '270'], true)) {
            return 'auto';
        }

        return $params['or'];
    }
}
