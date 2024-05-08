<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;

class Encode extends BaseManipulator
{
    /**
     * Perform output image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return EncodedImageInterface The encoded image.
     */
    public function run(ImageInterface $image): EncodedImageInterface
    {
        $format = $this->getFormat($image);
        $quality = $this->getQuality();
        $shouldInterlace = filter_var($this->getParam('interlace'), FILTER_VALIDATE_BOOLEAN);

        if ('pjpg' === $format) {
            $shouldInterlace = true;
            $format = 'jpg';
        }

        $encoderOptions = ['extension' => $format];
        switch ($format) {
            case 'avif':
            case 'heic':
            case 'tiff':
            case 'jpg':
            case 'webp':
                $encoderOptions['quality'] = $quality;
                // no break
            case 'jpg':
                $encoderOptions['progressive'] = $shouldInterlace;
                break;
            case 'gif':
            case 'png':
                $encoderOptions['interlaced'] = $shouldInterlace;
                break;
            default:
        }

        return $image->encodeByExtension(...$encoderOptions);
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

        if (
            !is_numeric($q)
            || $q < 0
            || $q > 100
        ) {
            return $default;
        }

        return (int) $q;
    }
}
