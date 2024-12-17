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

        if (MediaType::IMAGE_PJPEG === $mediaType) {
            $encoderOptions['progressive'] = true;
        }

        if (
            MediaType::IMAGE_PNG !== $mediaType
            || MediaType::IMAGE_GIF !== $mediaType
        ) {
            $encoderOptions['quality'] = $this->getQuality();
        } else {
            $encoderOptions['interlaced'] = filter_var($this->getParam('interlace'), FILTER_VALIDATE_BOOLEAN);
        }

        return $mediaType->format()->encoder(...array_filter($encoderOptions))->encode($image);
    }

    /**
     * Resolve media type.
     *
     * @param ImageInterface $image
     *
     * @throws \Exception
     *
     * @return MediaType
     */
    public function getMediaType(ImageInterface $image): MediaType
    {
        $fm = (string) $this->getParam('fm');

        if ('' !== $fm) {
            return self::supportedFormats()[$fm] ?? throw new \Exception("Invalid format provided: {$fm}");
        }

        try {
            return MediaType::from($image->origin()->mediaType());
        } catch (\ValueError) {
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
