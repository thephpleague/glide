<?php

namespace League\Glide\Manipulators\Helpers;

use Intervention\Image\Image;

class Dimension
{
    /**
     * The source image.
     * @var Image
     */
    protected $image;

    /**
     * The device pixel ratio.
     * @var double
     */
    protected $dpr;

    /**
     * Create dimension helper instance.
     * @param Image   $image The source image.
     * @param double $dpr   The device pixel ratio.
     */
    public function __construct(Image $image, float $dpr = 1)
    {
        $this->image = $image;
        $this->dpr = $dpr;
    }

    /**
     * Resolve the dimension.
     * @param  string $value The dimension value.
     * @return double|null The resolved dimension.
     */
    public function get(string $value): ?float
    {
        if (is_numeric($value) and $value > 0) {
            return (double) $value * $this->dpr;
        }

        if (preg_match('/^(\d{1,2}(?!\d)|100)(w|h)$/', $value, $matches)) {
            if ($matches[2] === 'h') {
                return (double) $this->image->height() * ((int) $matches[1] / 100);
            }

            return (double) $this->image->width() * ((int) $matches[1] / 100);
        }

        return null;
    }
}
