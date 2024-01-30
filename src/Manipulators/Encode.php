<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\ImageInterface;

class Encode extends BaseManipulator
{
    /**
     * Perform output image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $format = $this->getFormat($image);
        $quality = $this->getQuality();
        $driver = $image->driver();
        $interlace = false;

        if ('pjpg' === $format) {
            $interlace = true;

            $format = 'jpg';
        }

        $image = (new ImageManager($driver))->read(
            $image->encodeByExtension($format, $quality)->toString()
        );

        if ($interlace) {
            $image = $this->interlace($image, $driver);
        }

        return $image;
    }

    /**
     * Resolve format.
     *
     * @param ImageInterface $image The source image.
     *
     * @return string The resolved format.
     */
    public function getFormat(ImageInterface $image): string
    {
        $fm = (string) $this->getParam('fm');

        if ($fm && array_key_exists($fm, static::supportedFormats())) {
            return $fm;
        }

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        return array_search($image->origin()->mediaType(), static::supportedFormats(), true) ?: 'jpg';
    }

    /**
     * Get a list of supported image formats and MIME types.
     *
     * @return array<string,string>
     */
    public static function supportedFormats(): array
    {
        return [
            'avif' => 'image/avif',
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'pjpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'tiff' => 'image/tiff',
            'heic' => 'image/heic',
        ];
    }

    /**
     * Resolve quality.
     *
     * @return int The resolved quality.
     */
    public function getQuality(): int
    {
        $default = 90;
        $q = $this->getParam('q');

        if (!is_numeric($q)
            || $q < 0
            || $q > 100
        ) {
            return $default;
        }

        return (int) $q;
    }

    protected function interlace(ImageInterface $image, DriverInterface $driver): ImageInterface
    {
        $img = $image->core()->native();

        if ($driver instanceof ImagickDriver) {
            $img->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
        } elseif ($driver instanceof GdDriver) {
            imageinterlace($img, true);
        }

        return $image;
    }
}
