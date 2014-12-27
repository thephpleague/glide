<?php

namespace Glide;

use Glide\Exceptions\ManipulationException;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class Manipulator
{
    private $imageManager;
    private $manipulators;
    private $image;

    public function __construct(ImageManager $imageManager, $manipulators)
    {
        $this->imageManager = $imageManager;
        $this->manipulators = $manipulators;
    }

    public function setImage($source)
    {
        $this->image = $this->imageManager->make($source);
    }

    public function validate(Request $request)
    {
        $errors = [];

        foreach ($this->manipulators as $manipulator) {
            $errors = array_merge($errors, $manipulator->validate($request, $this->image));
        }

        if ($errors) {
            throw new ManipulationException($errors);
        }
    }

    public function run(Request $request)
    {
        foreach ($this->manipulators as $manipulator) {
            $manipulator->run($request, $this->image);
        }

        return $this->image->getEncoded();
    }
}
