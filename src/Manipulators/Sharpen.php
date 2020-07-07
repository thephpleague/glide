<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $sharp
 */
class Sharpen extends BaseManipulator
{
    /**
     * Perform sharpen image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        $sharpen = $this->getSharpen();

        if ($sharpen !== null) {
            $image->sharpen($sharpen);
        }

        return $image;
    }

    /**
     * Resolve sharpen amount.
     * @return int|null The resolved sharpen amount.
     */
    public function getSharpen(): ?int
    {
        if (!is_numeric($this->sharp)) {
            return null;
        }

        if ($this->sharp < 0 or $this->sharp > 100) {
            return null;
        }

        return (int) $this->sharp;
    }
}
