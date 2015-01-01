<?php

namespace Glide;

use Glide\Interfaces\API as APIInterface;
use Intervention\Image\ImageManager;

class API implements APIInterface
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
        $this->imageManager = $imageManager;
        $this->manipulators = $manipulators;
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
