<?php

namespace League\Glide;

use Intervention\Image\ImageManager;
use InvalidArgumentException;
use League\Glide\Interfaces\Api as ApiInterface;
use League\Glide\Interfaces\Manipulator;

class Api implements ApiInterface
{
    /**
     * Intervention image manager.
     * @var ImageManager
     */
    private $imageManager;

    /**
     * Collection of manipulators.
     * @var Array
     */
    private $manipulators;

    /**
     * Create API instance.
     * @param ImageManager $imageManager Intervention image manager.
     * @param Array        $manipulators Collection of manipulators.
     */
    public function __construct(ImageManager $imageManager, Array $manipulators)
    {
        $this->setImageManager($imageManager);
        $this->setManipulators($manipulators);
    }

    /**
     * Set the image manager.
     * @param ImageManager $imageManager Intervention image manager.
     */
    public function setImageManager(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Get the image manager.
     * @return ImageManager Intervention image manager.
     */
    public function getImageManager()
    {
        return $this->imageManager;
    }

    /**
     * Set the manipulators.
     * @param Array $manipulators Collection of manipulators.
     */
    public function setManipulators(Array $manipulators)
    {
        foreach ($manipulators as $manipulator) {
            if (!($manipulator instanceof Manipulator)) {
                throw new InvalidArgumentException('Not a valid manipulator.');
            }
        }

        $this->manipulators = $manipulators;
    }

    /**
     * Get the manipulators.
     * @return Array Collection of manipulators.
     */
    public function getManipulators()
    {
        return $this->manipulators;
    }

    /**
     * Perform image manipulations.
     * @param  Request $request The request object.
     * @param  string  $source  Source image binary data.
     * @return string  Manipulated image binary data.
     */
    public function run(Request $request, $source)
    {
        $image = $this->imageManager->make($source);

        foreach ($this->manipulators as $manipulator) {
            $manipulator->run($request, $image);
        }

        return $image->getEncoded();
    }
}
