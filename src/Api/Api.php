<?php

namespace League\Glide\Api;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\ManipulatorInterface;

class Api implements ApiInterface
{
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
     * Create API instance.
     *
     * @param ImageManager $imageManager Intervention image manager.
     * @param array        $manipulators Collection of manipulators.
     */
    public function __construct(ImageManager $imageManager, array $manipulators)
    {
        $this->setImageManager($imageManager);
        $this->setManipulators($manipulators);
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
     * @param ManipulatorInterface[] $manipulators Collection of manipulators.
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
        $image = $this->imageManager->read($source);

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
}
