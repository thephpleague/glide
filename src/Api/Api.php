<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use League\Glide\Manipulators\ManipulatorInterface;

class Api implements ApiInterface
{
    public const GLOBAL_API_PARAMS = [
        'p', // preset
        'q', // quality
        'fm', // format
        's', // signature
    ];

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
     * Image decoder.
     */
    protected ?Decoder $decoder = null;

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
     * @param Decoder|null           $decoder      Image decoder.
     */
    public function __construct(ImageManagerInterface $imageManager, array $manipulators, ?Encoder $encoder = null, ?Decoder $decoder = null)
    {
        $this->setImageManager($imageManager);
        $this->setManipulators($manipulators);
        $this->setApiParams();
        $this->encoder = $encoder;
        $this->decoder = $decoder;
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
     * Set the decoder.
     *
     * @param Decoder $decoder Image decoder.
     */
    public function setDecoder(Decoder $decoder): void
    {
        $this->decoder = $decoder;
    }

    /**
     * Get the decoder.
     *
     * @return Decoder Image decoder.
     */
    public function getDecoder(): Decoder
    {
        return $this->decoder ??= new Decoder();
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
     * Decode the source image.
     *
     * @param string               $source Source image binary data.
     * @param array<string, mixed> $params The manipulation params.
     *
     * @return ImageInterface The decoded image.
     */
    public function decode(string $source, array $params): ImageInterface
    {
        return $this->getDecoder()->setParams($params)->run($source, $this->imageManager, $this->manipulators);
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
