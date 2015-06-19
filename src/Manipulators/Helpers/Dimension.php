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
     * Create dimension helper instance.
     * @param Image $image The source image.
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Resolve the dimension.
     * @param  string $value The dimension value.
     * @return double The resolved dimension.
     */
    public function get($value)
    {
        if (is_numeric($value) and $value > 0) {
            return (double) $value;
        }

        if (preg_match('/^(\d{1,2}(?!\d)|100)(w|h)$/', $value)) {
            $type = substr($value, -1);
            $value = substr($value, 0, -1);

            if ($type === 'w') {
                return (double) $this->image->width() * ($value / 100);
            }

            if ($type === 'h') {
                return (double) $this->image->height() * ($value / 100);
            }
        }
    }
}
