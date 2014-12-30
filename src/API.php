<?php

namespace Glide;

use Glide\Interfaces\API as APIInterface;
use Intervention\Image\ImageManager;

class API implements APIInterface
{
    private $imageManager;
    private $manipulators;

    public function __construct(ImageManager $imageManager, $manipulators)
    {
        $this->imageManager = $imageManager;
        $this->manipulators = $manipulators;
    }

    public function run(Request $request, $source)
    {
        $image = $this->imageManager->make($source);

        foreach ($this->manipulators as $manipulator) {
            $manipulator->run($request, $image);
        }

        return $image->getEncoded();
    }
}
