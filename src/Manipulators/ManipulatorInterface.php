<?php

declare(strict_types=1);

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
    public function setParams(array $params): static;

    /**
     * Get a specific manipulation param.
     */
    public function getParam(string $name): mixed;

    /**
     * Get the names of the manipulator API parameters.
     *
     * @rerun list<string>
     */
    public function getApiParams(): array;

    /**
     * Perform the image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface;
}
