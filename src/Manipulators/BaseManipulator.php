<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

abstract class BaseManipulator implements ManipulatorInterface
{
    /**
     * The manipulation params.
     */
    public array $params = [];

    /**
     * Set the manipulation params.
     *
     * @param array $params The manipulation params.
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get a specific manipulation param.
     *
     * @param string $name The manipulation name.
     *
     * @return mixed The manipulation value.
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
    }

    /**
     * Perform the image manipulation.
     *
     * @return ImageInterface The manipulated image.
     */
    abstract public function run(ImageInterface $image): ImageInterface;
}
