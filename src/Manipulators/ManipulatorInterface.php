<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;

interface ManipulatorInterface
{
    /**
     * Set the manipulation params.
     *
     * @param array $params The manipulation params.
     *
     * @return $this
     */
    public function setParams(array $params);

    /**
     * Perform the image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface;
}
