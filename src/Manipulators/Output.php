<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\Request;

class Output implements ManipulatorInterface
{
    /**
     * Perform output image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        $format = $this->getFormat($image, $request->get('fm'));
        $quality = $this->getQuality($request->get('q'));

        if ($format === 'pjpg') {
            $image->interlace();
            $format = 'jpg';
        }

        return $image->encode($format, $quality);
    }

    /**
     * Resolve format.
     * @param  string $format The format.
     * @return string The resolved format.
     */
    public function getFormat(Image $image, $format)
    {
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
     * @param  string $quality The quality.
     * @return string The resolved quality.
     */
    public function getQuality($quality)
    {
        $default = 90;

        if (is_null($quality)) {
            return $default;
        }

        if (!is_numeric($quality)) {
            return $default;
        }

        if ($quality < 0 or $quality > 100) {
            return $default;
        }

        return (int) $quality;
    }
}
