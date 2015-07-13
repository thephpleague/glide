<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Glide\Manipulators\Helpers\Color;

class Background extends BaseManipulator
{
    /**
     * Perform blur image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image)
    {
        $color = (new Color($this->bg))->formatted();

        if ($color) {
            $new = $image->getDriver()->newImage($image->width(), $image->height(), $color);
            $new->mime = $image->mime;
            $image = $new->insert($image);
        }

        return $image;
    }
}
