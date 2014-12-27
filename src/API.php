<?php

namespace Glide;

use Glide\Exceptions\ManipulationException;
use Glide\Interfaces\API as APIInterface;
use Intervention\Image\Image;
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

    public function validate(Request $request, $source)
    {
        $image = $this->imageManager->make($source);

        $errors = [];

        foreach ($this->manipulators as $manipulator) {
            $errors = array_merge($errors, $manipulator->validate($request, $image));
        }

        if ($errors) {
            throw new ManipulationException($errors);
        }
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
