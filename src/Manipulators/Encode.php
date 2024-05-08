<?php

namespace League\Glide\Manipulators;

use Intervention\Image\FileExtension;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\MediaType;

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
            $format = FileExtension::JPG->value;
        }

        $encoderOptions = ['extension' => $format];
        switch ($format) {
            case FileExtension::AVIF->value:
            case FileExtension::HEIC->value:
            case FileExtension::AVIF->value:
            case FileExtension::TIFF->value:
            case FileExtension::AVIF->value:
            case FileExtension::JPG->value:
            case FileExtension::WEBP->value:
                $encoderOptions['quality'] = $quality;
                // no break
            case FileExtension::JPG->value:
                $encoderOptions['progressive'] = $shouldInterlace;
                break;
            case FileExtension::GIF->value:
            case FileExtension::PNG->value:
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
        return array_search($image->origin()->mediaType(), static::supportedFormats(), true) ?: FileExtension::JPG->value;
    }

    /**
     * Get a list of supported image formats and MIME types.
     *
     * @return array<string,string>
     */
    public static function supportedFormats(): array
    {
        return [
            FileExtension::AVIF->value => MediaType::IMAGE_AVIF->value,
            FileExtension::GIF->value => MediaType::IMAGE_GIF->value,
            FileExtension::JPEG->value => MediaType::IMAGE_JPEG->value,
            FileExtension::JPG->value => MediaType::IMAGE_JPEG->value,
            'pjpg' => MediaType::IMAGE_JPEG->value,
            FileExtension::PNG->value => MediaType::IMAGE_PNG->value,
            FileExtension::WEBP->value => MediaType::IMAGE_WEBP->value,
            FileExtension::TIF->value => MediaType::IMAGE_TIFF->value,
            FileExtension::HEIC->value => MediaType::IMAGE_HEIC->value,
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
