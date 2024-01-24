<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;


/**
 * @property string $fm
 * @property string $q
 */
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

        if (in_array($format, ['jpg', 'pjpg'], true)) {
            $image = (new ImageManager($driver))
                ->create($image->width(), $image->height())
                ->fill('ffffff')
                ->place($image, 'top-left', 0, 0);
        }

        if (in_array($format, ['png', 'pjpg'], true)) {
            $i = $image->core()->native();
            if ($driver instanceof ImagickDriver) {
                $i->setInterlaceScheme(3); // 3 = Imagick::INTERLACE_PLANE constant
            } else if ($driver instanceof GdDriver) {
                imageinterlace($i, true);
            }

            if ($format === 'pjpg') {
                $format = 'jpg';
            }
        }

        return (new ImageManager($driver))->read(
            $image->encodeByExtension($format, $quality)->toDataUri()
        );
    }

    /**
     * Resolve format.
     *
     * @param ImageInterface $image The source image.
     *
     * @return string The resolved format.
     */
    public function getFormat(ImageInterface $image)
    {
        if (array_key_exists($this->fm, static::supportedFormats())) {
            return $this->fm;
        }

        return array_search($image->encode(new AutoEncoder())->mediaType(), static::supportedFormats(), true) ?: 'jpg';
    }

    /**
     * Get a list of supported image formats and MIME types.
     *
     * @return array<string,string>
     */
    public static function supportedFormats()
    {
        return [
            'avif' => 'image/avif',
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'pjpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'tiff' => 'image/tiff',
        ];
    }

    /**
     * Resolve quality.
     *
     * @return int The resolved quality.
     */
    public function getQuality()
    {
        $default = 90;

        if (!is_numeric($this->q)) {
            return $default;
        }

        if ($this->q < 0 or $this->q > 100) {
            return $default;
        }

        return (int) $this->q;
    }
}
