<?php

namespace League\Glide\Api\Manipulator;

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
        return $image->encode(
            $this->getFormat($image, $request->get('fm')),
            $this->getQuality($request->get('q'))
        );
    }

    /**
     * Resolve format.
     * @param  string $format The format.
     * @return string The resolved format.
     */
    public function getFormat(Image $image, $format)
    {
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];

        if (in_array($format, $allowed, true)) {
            return $format;
        }

        $mime = $image->mime();
        if (isset($allowed[$mime])) {
            return $allowed[$mime];
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

        if (!ctype_digit($quality)) {
            return $default;
        }

        if ($quality < 0 or $quality > 100) {
            return $default;
        }

        return $quality;
    }
}
