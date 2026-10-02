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

class Api implements ApiInterface
{
    public const GLOBAL_API_PARAMS = [
        'p', // preset
        'q', // quality
        'fm', // format
        's', // signature
    ];

    /**
     * Minimum ratio between the shrunk-on-load source and the requested size, so the final resize
     * still has enough pixels to produce a sharp result.
     */
    public const SHRINK_ON_LOAD_MARGIN = 2;

    /**
     * Intervention image manager.
     */
    protected ImageManagerInterface $imageManager;

    /**
     * Collection of manipulators.
     *
     * @var array<ManipulatorInterface>
     */
    protected array $manipulators;

    /**
     * Image encoder.
     */
    protected ?Encoder $encoder = null;

    /**
     * API parameters.
     *
     * @var list<string>
     */
    protected array $apiParams;

    /**
     * Create API instance.
     *
     * @param ImageManagerInterface $imageManager Intervention image manager.
     * @param array<ManipulatorInterface> $manipulators Collection of manipulators.
     * @param Encoder|null           $encoder      Image encoder.
     */
    public function __construct(ImageManagerInterface $imageManager, array $manipulators, ?Encoder $encoder = null)
    {
        $this->setImageManager($imageManager);
        $this->setManipulators($manipulators);
        $this->setApiParams();
        $this->encoder = $encoder;
    }

    /**
     * Set the image manager.
     *
     * @param ImageManagerInterface $imageManager Intervention image manager.
     */
    public function setImageManager(ImageManagerInterface $imageManager): void
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Get the image manager.
     *
     * @return ImageManagerInterface Intervention image manager.
     */
    public function getImageManager(): ImageManagerInterface
    {
        return $this->imageManager;
    }

    /**
     * Set the manipulators.
     *
     * @param array<ManipulatorInterface> $manipulators Collection of manipulators.
     */
    public function setManipulators(array $manipulators): void
    {
        foreach ($manipulators as $manipulator) {
            if (!$manipulator instanceof ManipulatorInterface) {
                throw new \InvalidArgumentException('Not a valid manipulator.');
            }
        }

        $this->manipulators = $manipulators;
    }

    /**
     * Get the manipulators.
     *
     * @return array<ManipulatorInterface> Collection of manipulators.
     */
    public function getManipulators(): array
    {
        return $this->manipulators;
    }

    /**
     * Set the encoder.
     *
     * @param Encoder $encoder Image encoder.
     */
    public function setEncoder(Encoder $encoder): void
    {
        $this->encoder = $encoder;
    }

    /**
     * Get the encoder.
     *
     * @return Encoder Image encoder.
     */
    public function getEncoder(): Encoder
    {
        return $this->encoder ??= new Encoder();
    }

    /**
     * Perform image manipulations.
     *
     * @param string                $source Source image binary data.
     * @param array<string, mixed>  $params The manipulation params.
     *
     * @return string Manipulated image binary data.
     */
    public function run(string $source, array $params): string
    {
        $image = $this->decode($source, $params);

        foreach ($this->manipulators as $manipulator) {
            $manipulator->setParams($params);
            $image = $manipulator->run($image);
        }

        return $this->encode($image, $params);
    }

    /**
     * Decode the source image, shrinking large JPEGs on load when the requested size allows it.
     *
     * Only factors dividing both dimensions are used, so the aspect ratio and every output size stay exact.
     *
     * @param string               $source Source image binary data.
     * @param array<string, mixed> $params The manipulation params.
     *
     * @return ImageInterface The decoded image.
     */
    protected function decode(string $source, array $params): ImageInterface
    {
        $edge = $this->getShrinkOnLoadEdge($params);
        $driver = $this->imageManager instanceof ImageManager ? $this->imageManager->driver : null;
        $info = $edge !== null && $driver !== null ? @getimagesizefromstring($source) : false;

        if ($edge === null || $driver === null || $info === false || $info[2] !== IMAGETYPE_JPEG) {
            return $this->imageManager->decodeBinary($source);
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
                    return $this->imageManager->decode($imagick);
                }
            } catch (\ImagickException) {
                // Let the regular decoder report the error.
            }
        }

        $config = $driver->config();
        if (!$config->autoOrientation || !function_exists('exif_read_data')) {
            return $this->imageManager->decodeBinary($source);
        }

        // Rotating the full-size source is the most expensive step on every driver: shrink EXIF-rotated images first.
        $config->autoOrientation = false;
        try {
            $image = $this->imageManager->decodeBinary($source);
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
     * @param array<string, mixed> $params The manipulation params.
     */
    protected function getShrinkOnLoadEdge(array $params): ?int
    {
        foreach ($this->manipulators as $manipulator) {
            if ($manipulator instanceof Size) {
                $edge = $manipulator->setParams($params)->getRequiredSourceEdge();

                return $edge === null ? null : $edge * self::SHRINK_ON_LOAD_MARGIN;
            }

            // Crop coordinates, like any unknown manipulator running before the resize, need the full-size source.
            if (!$manipulator instanceof Orientation && !($manipulator instanceof Crop && empty($params['crop']))) {
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

    /**
     * Perform image encoding to a given format.
     *
     * @param ImageInterface        $image  Image object
     * @param array<string, mixed>  $params the manipulator params
     *
     * @return string Manipulated image binary data
     */
    public function encode(ImageInterface $image, array $params): string
    {
        return $this->getEncoder()->setParams($params)->run($image)->toString();
    }

    /**
     * Sets the API parameters for all manipulators.
     *
     * @return list<string>
     */
    public function setApiParams(): array
    {
        $this->apiParams = self::GLOBAL_API_PARAMS;

        foreach ($this->manipulators as $manipulator) {
            $this->apiParams = array_merge($this->apiParams, $manipulator->getApiParams());
        }

        return $this->apiParams = array_values(array_unique($this->apiParams));
    }

    /**
     * Retun the list of API params.
     *
     * @return list<string>
     */
    public function getApiParams(): array
    {
        return $this->apiParams;
    }
}
