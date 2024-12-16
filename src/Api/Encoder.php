<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\MediaType;

/**
 * Encoder Api class to convert a given image to a specific format.
 */
class Encoder
{
    /**
     * The manipulation params.
     */
    protected array $params;

    /**
     * Class constructor.
     *
     * @param array $params the manipulator params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * Set the manipulation params.
     *
     * @param array $params The manipulation params.
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get a specific manipulation param.
     */
    public function getParam(string $name): mixed
    {
        return array_key_exists($name, $this->params)
            ? $this->params[$name]
            : null;
    }

    /**
     * Perform output image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return EncodedImageInterface The encoded image.
     */
    public function run(ImageInterface $image): EncodedImageInterface
    {
        $mediaType = $this->getMediaType($image);
        $quality = $this->getQuality();
        $shouldInterlace = filter_var($this->getParam('interlace'), FILTER_VALIDATE_BOOLEAN);

        $encoderOptions = array_filter([
            'quality' => $quality,
            'interlaced' => $mediaType === MediaType::IMAGE_PNG ? $shouldInterlace : null,
        ]);

        return $image->encodeByMediaType($mediaType, ...$encoderOptions);
    }

    public function getMediaType(ImageInterface $image): MediaType
    {
        $fm = (string) $this->getParam('fm');

        if ($fm && array_key_exists($fm, static::supportedFormats())) {
            return match ($fm) {
                'avif' => MediaType::IMAGE_AVIF,
                'gif' => MediaType::IMAGE_GIF,
                'jpg' => MediaType::IMAGE_JPEG,
                'pjpg' => MediaType::IMAGE_PJPEG,
                'png' => MediaType::IMAGE_PNG,
                'webp' => MediaType::IMAGE_WEBP,
                'tiff' => MediaType::IMAGE_TIFF,
                'heic' => MediaType::IMAGE_HEIC,
            };
        }

        try {
            return MediaType::tryFrom($image->origin()->mediaType());
        } catch (\Exception) {
            return MediaType::IMAGE_JPEG;
        }
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

        try {
            $format    = MediaType::tryFrom($image->origin()->mediaType())->format();
            $extension = $format->fileExtension()->value;

            return isset(static::supportedFormats()[$extension]) ? $extension : 'jpg';
        } catch (\Exception) {
            return 'jpg';
        }
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
        $default = 85;
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
