<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Manipulators\Helpers\Color;

class Background implements ManipulatorInterface
{
    /**
     * Perform blur image manipulation.
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        $color = $this->getBackground($params);

        if ($color) {
            $new = $image->getDriver()->newImage($image->width(), $image->height(), $color);
            $new->mime = $image->mime;
            $image = $new->insert($image);
        }

        return $image;
    }

    /**
     * Get background color.
     * @param  array  $params The manipulation params.
     * @return string The background color.
     */
    public function getBackground($params)
    {
        if (!isset($params['bg'])) {
            return;
        }

        return (new Color($params['bg']))->formatted();
    }
}
