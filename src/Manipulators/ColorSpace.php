<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $cs
 */
class ColorSpace extends BaseManipulator
{
    /**
     * Possible color spaces
     *
     * @var array
     */
    private $colorSpaces = [];

    /**
     * Perform color space image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image)
    {
        if ($image->getDriver()->getDriverName() !== 'Imagick') {
            return $image;
        }

        $this->colorSpaces = [
            'rgb' => \Imagick::COLORSPACE_RGB,
            'srgb' => \Imagick::COLORSPACE_SRGB,
            'cmyk' => \Imagick::COLORSPACE_CMYK
        ];

        $colorSpace = $this->getColorSpace();

        if ($colorSpace !== null) {
            $image->getCore()->transformimagecolorspace($colorSpace);
        }

        return $image;
    }

    /**
     * Resolve color space value.
     * @return integer The resolved color space value.
     */
    public function getColorSpace()
    {
        if (!in_array($this->cs, array_keys($this->colorSpaces), true)) {
            return;
        }

        return $this->colorSpaces[$this->cs];
    }
}