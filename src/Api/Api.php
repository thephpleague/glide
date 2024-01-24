<?php

namespace League\Glide\Api;

use Intervention\Image\ImageManager;
use League\Glide\Manipulators\Encode;
use League\Glide\Manipulators\ManipulatorInterface;

class Api implements ApiInterface
{
    /**
     * Intervention image manager.
     *
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * Collection of manipulators.
     *
     * @var ManipulatorInterface[]
     */
    protected $manipulators;

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
     *
     * @return void
     */
    public function setImageManager(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Get the image manager.
     *
     * @return ImageManager Intervention image manager.
     */
    public function getImageManager()
    {
        return $this->imageManager;
    }

    /**
     * Set the manipulators.
     *
     * @param ManipulatorInterface[] $manipulators Collection of manipulators.
     *
     * @return void
     */
    public function setManipulators(array $manipulators)
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
    public function getManipulators()
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
    public function run($source, array $params)
    {
        $image = $this->imageManager->read($source);

        foreach ($this->manipulators as $manipulator) {
            $manipulator->setParams($params);

            $image = $manipulator->run($image);
        }

        $encode = new Encode();
        $encode->setParams($params);

        switch ($encode->fm) {
            case 'avif':
                $encodedImage = $image->toAvif($encode->getQuality());
                break;

            case 'gif':
                $encodedImage = $image->toGif($encode->getQuality());
                break;

            case 'png':
                $encodedImage = $image->toPng($encode->getQuality());
                break;

            case 'webp':
                $encodedImage = $image->toWebp($encode->getQuality());
                break;

            case 'tiff':
                $encodedImage = $image->toTiff($encode->getQuality());
                break;

            case 'jpg':
            case 'pjpg':
            default:
                $encodedImage = $image->toJpeg($encode->getQuality());
                break;
        }

        return $encodedImage->toString();
    }
}
