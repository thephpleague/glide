<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $gam
 */
class Gamma extends BaseManipulator
{
    /**
     * Perform gamma image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        $gamma = $this->getGamma();

        if ($gamma) {
            $image->gamma($gamma);
        }

        return $image;
    }

    /**
     * Resolve gamma amount.
     * @return float|null The resolved gamma amount.
     */
    public function getGamma(): ?float
    {
        if (!preg_match('/^[0-9]\.*[0-9]*$/', (string) $this->gam)) {
            return null;
        }

        if ($this->gam < 0.1 or $this->gam > 9.99) {
            return null;
        }

        return (float) $this->gam;
    }
}
