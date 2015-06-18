<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

class Sharpen implements ManipulatorInterface
{
    /**
     * Perform sharpen image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $sharpen = $this->getSharpen($params);

        if ($sharpen) {
            $image->sharpen($sharpen);
        }

        return $image;
    }

    /**
     * Resolve sharpen amount.
     * @param  array  $params The manipulation params.
     * @return string The resolved sharpen amount.
     */
    public function getSharpen($params)
    {
        if (!isset($params['sharp'])) {
            return;
        }

        if (!is_numeric($params['sharp'])) {
            return;
        }

        if ($params['sharp'] < 0 or $params['sharp'] > 100) {
            return;
        }

        return (int) $params['sharp'];
    }
}
