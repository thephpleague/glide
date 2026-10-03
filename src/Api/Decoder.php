<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use League\Glide\Manipulators\Crop;
use League\Glide\Manipulators\ManipulatorInterface;
use League\Glide\Manipulators\Orientation;
use League\Glide\Manipulators\Size;

/**
 * Decoder Api class to decode a source image, shrinking large JPEGs on load when the requested size allows it.
 */
class Decoder
{
    /**
     * Minimum ratio between the shrunk-on-load source and the requested size, so the final resize
     * still has enough pixels to produce a sharp result.
     */
    public const int SHRINK_ON_LOAD_MARGIN = 2;

    /**
     * The manipulation params.
     *
     * @var array<string, mixed>
     */
    protected array $params;

    /**
     * Class constructor.
     *
     * @param array<string, mixed> $params the manipulator params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * Set the manipulation params.
     *
     * @param array<string, mixed> $params The manipulation params.
     *
     * @return $this
     */
    public function setParams(array $params): static
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
     * Decode the source image, shrinking large JPEGs on load when the requested size allows it.
     *
     * Only factors dividing both dimensions are used, so the aspect ratio and every output size stay exact.
     *
     * @param string                      $source       Source image binary data.
     * @param ImageManagerInterface       $imageManager Intervention image manager.
     * @param array<ManipulatorInterface> $manipulators The manipulators which run on the decoded image.
     *
     * @return ImageInterface The decoded image.
     */
    public function run(string $source, ImageManagerInterface $imageManager, array $manipulators): ImageInterface
    {
        $edge = $this->getShrinkOnLoadEdge($manipulators);
        $driver = $imageManager instanceof ImageManager ? $imageManager->driver : null;
        $info = $edge !== null && $driver !== null ? @getimagesizefromstring($source) : false;

        if ($info === false || $info[2] !== IMAGETYPE_JPEG) {
            return $imageManager->decodeBinary($source);
        }

        [$width, $height] = $info;

        // Imagick: let libjpeg decode directly at 1/2, 1/4 or 1/8 scale. Native objects carry no EXIF data, so the
        // Orientation manipulator could not align the image when the decoder does not do it.
        $factor = self::getShrinkFactor($width, $height, $edge, [8, 4, 2]);
        if ($driver instanceof ImagickDriver && $factor > 1 && $driver->config()->autoOrientation) {
            try {
                $imagick = new \Imagick();
                $imagick->setOption('jpeg:size', intdiv($width, $factor) . 'x' . intdiv($height, $factor));
                $imagick->readImageBlob($source);

                if ($imagick->getImageWidth() * $factor === $width && $imagick->getImageHeight() * $factor === $height) {
                    return $imageManager->decode($imagick);
                }
            } catch (\ImagickException) {
                // Let the regular decoder report the error.
            }
        }

        $config = $driver->config();
        if (!$config->autoOrientation || !function_exists('exif_read_data')) {
            return $imageManager->decodeBinary($source);
        }

        // Rotating the full-size source is the most expensive step on every driver: shrink EXIF-rotated images first.
        $config->autoOrientation = false;
        try {
            $image = $imageManager->decodeBinary($source);
        } finally {
            $config->autoOrientation = true;
        }

        if ((int) $image->exif('IFD0.Orientation') <= 1) {
            return $image;
        }

        $factor = self::getShrinkFactor($width, $height, $edge, range(max(intdiv(min($width, $height), $edge), 2), 2));
        if ($factor > 1) {
            $image->resize(intdiv($width, $factor), intdiv($height, $factor));
        }

        return $image->orient();
    }

    /**
     * Resolve the shortest source side the resize needs, or null when shrinking on load is not safe.
     *
     * @param array<ManipulatorInterface> $manipulators The manipulators which run on the decoded image.
     */
    protected function getShrinkOnLoadEdge(array $manipulators): ?int
    {
        foreach ($manipulators as $manipulator) {
            if ($manipulator instanceof Size) {
                $edge = $manipulator->setParams($this->params)->getRequiredSourceEdge();

                return $edge === null ? null : $edge * self::SHRINK_ON_LOAD_MARGIN;
            }

            // Crop coordinates, like any unknown manipulator running before the resize, need the full-size source.
            if (!$manipulator instanceof Orientation && !($manipulator instanceof Crop && empty($this->params['crop']))) {
                return null;
            }
        }

        return null;
    }

    /**
     * Find the largest candidate factor dividing both dimensions that keeps the shortest side at least $edge long.
     *
     * @param list<int> $candidates Candidate factors, from largest to smallest.
     */
    private static function getShrinkFactor(int $width, int $height, int $edge, array $candidates): int
    {
        foreach ($candidates as $factor) {
            if ($width % $factor === 0 && $height % $factor === 0 && min($width, $height) / $factor >= $edge) {
                return $factor;
            }
        }

        return 1;
    }
}
