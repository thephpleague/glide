<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Decoders\BinaryImageDecoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
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
    protected ImageManager $imageManager;

    /**
     * Collection of manipulators.
     *
     * @var ManipulatorInterface[]
     */
    protected array $manipulators;

    /**
     * API parameters.
     *
     * @var list<string>
     */
    protected array $apiParams;

    /**
     * Create API instance.
     *
     * @param ImageManager $imageManager Intervention image manager.
     * @param array        $manipulators Collection of manipulators.
     */
    public function __construct(ImageManager $imageManager, array $manipulators)
    {
        $this->setImageManager($imageManager);
        $this->setManipulators($manipulators);
        $this->setApiParams();
    }

    /**
     * Set the image manager.
     *
     * @param ImageManager $imageManager Intervention image manager.
     */
    public function setImageManager(ImageManager $imageManager): void
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Get the image manager.
     *
     * @return ImageManager Intervention image manager.
     */
    public function getImageManager(): ImageManager
    {
        return $this->imageManager;
    }

    /**
     * Set the manipulators.
     *
     * @param array $manipulators Collection of manipulators.
     */
    public function setManipulators(array $manipulators): void
    {
        foreach ($manipulators as $manipulator) {
            if (!($manipulator instanceof ManipulatorInterface)) {
                throw new \InvalidArgumentException('Not a valid manipulator.');
            }
        }

        $this->manipulators = $manipulators;
    }

    /**
     * Get the manipulators.
     *
     * @return array Collection of manipulators.
     */
    public function getManipulators(): array
    {
        return $this->manipulators;
    }

    /**
     * Perform image manipulations.
     *
     * @param string $source Source image binary data.
     * @param array  $params The manipulation params.
     *
     * @return string Manipulated image binary data.
     */
    public function run(string $source, array $params): string
    {
        $image = $this->imageManager->read($source, BinaryImageDecoder::class);

        foreach ($this->manipulators as $manipulator) {
            $manipulator->setParams($params);
            $image = $manipulator->run($image);
        }

        return $this->encode($image, $params);
    }

    /**
     * Perform image encoding to a given format.
     *
     * @param ImageInterface $image  Image object
     * @param array          $params the manipulator params
     *
     * @return string Manipulated image binary data
     */
    public function encode(ImageInterface $image, array $params): string
    {
        $encoder = new Encoder($params);
        $encoded = $encoder->run($image);

        return $encoded->toString();
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
            foreach ($manipulator->getApiParams() as $param) {
                $this->apiParams[] = $param;
            }
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
