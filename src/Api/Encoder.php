<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Format;
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
            'interlaced' => MediaType::IMAGE_PNG === $mediaType ? $shouldInterlace : null,
        ]);

        return $image->encodeByMediaType($mediaType, ...$encoderOptions);
    }

    public function getMediaType(ImageInterface $image): MediaType
    {
        $fm = (string) $this->getParam('fm');

        if ('' !== $fm) {
            $mediaType = self::supportedFormats()[$fm] ?? throw new \Exception("Invalid format provided: {$fm}");
        } else {
            try {
                $mediaType = MediaType::tryFrom($image->origin()->mediaType());
            } catch (\Exception) {
                $mediaType = MediaType::IMAGE_JPEG;
            }
        }

        return $image->driver()->supports($mediaType) ? $mediaType : throw new \Exception("Unsupported format: {$mediaType->value}");
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
        try {
            $mediaType = $this->getMediaType($image);

            return $mediaType->format()->fileExtension()->value;
        } catch (\Exception) {
            return 'jpg';
        }
    }

    /**
     * Get a list of supported image formats and MIME types.
     *
     * @return array<string,MediaType>
     */
    public static function supportedFormats(): array
    {
        return [
            'avif' => MediaType::IMAGE_AVIF,
            'bmp' => MediaType::IMAGE_BMP,
            'gif' => MediaType::IMAGE_GIF,
            'heic' => MediaType::IMAGE_HEIC,
            'jpg' => MediaType::IMAGE_JPEG,
            'pjpg' => MediaType::IMAGE_PJPEG,
            'png' => MediaType::IMAGE_PNG,
            'tiff' => MediaType::IMAGE_TIFF,
            'webp' => MediaType::IMAGE_WEBP,
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