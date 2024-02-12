<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

abstract class BaseManipulator implements ManipulatorInterface
{
    /**
     * The manipulation params.
     */
    protected array $params = [];

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
     */
    public function getParam(string $name): mixed
    {
        return array_key_exists($name, $this->params)
            ? $this->params[$name]
            : null;
    }

    /**
     * Perform the image manipulation.
     *
     * @return ImageInterface The manipulated image.
     */
    abstract public function run(ImageInterface $image): ImageInterface;
}
