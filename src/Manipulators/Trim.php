<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;

/**
 * @property string $trim
 */
class Trim extends BaseManipulator
{
    /**
     * Perform trim image manipulation.
     *
     * @param Image $image The source image.
     *
     * @return Image The manipulated image.
     */
    public function run(Image $image)
    {
        if ($trim = $this->getTrim()) {
            list($base, $away, $tolerance, $feather) = $trim;
            return $image->trim($base, $away, $tolerance, $feather);
        }

        return $image;
    }

    /**
     * Resolve trim.
     *
     * @return array|null The resolved trim.
     */
    public function getTrim()
    {
        if (!$this->trim) {
            return;
        }

        $values = explode(',', $this->trim);

        $base = $this->getBase(isset($values[0]) ? $values[0] : null);
        $away = $this->getAway(isset($values[1]) ? $values[1] : null);
        $tolerance = $this->getTolerance(isset($values[2]) ? $values[2] : null);
        $feather = $this->getFeather(isset($values[3]) ? $values[3] : null);

        return [$base, $away, $tolerance, $feather];
    }

    /**
     * Resolve the base.
     *
     * @param string $base The raw base.
     *
     * @return string The resolved base.
     */
    public function getBase($base)
    {
        if (!in_array($base, ['top-left', 'bottom-right', 'transparent'], true)) {
            return 'top-left';
        }

        return $base;
    }

    /**
     * Resolve the away.
     *
     * @param string $away The raw away.
     *
     * @return array|null The resolved away array.
     */
    public function getAway($away)
    {
        if (null === $away || preg_match('/[^tblr]/', $away)) {
            return;
        }

        $aways = [];

        if (strpos($away, 't') !== false) {
            $aways[] = 'top';
        }

        if (strpos($away, 'b') !== false) {
            $aways[] = 'bottom';
        }

        if (strpos($away, 'l') !== false) {
            $aways[] = 'left';
        }

        if (strpos($away, 'r') !== false) {
            $aways[] = 'right';
        }

        if (empty($aways)) {
            return;
        }

        return $aways;
    }

    /**
     * Resolve the tolerance.
     *
     * @param string $tolerance The raw tolerance.
     *
     * @return int|null The resolved tolerance.
     */
    public function getTolerance($tolerance)
    {
        if (!is_numeric($tolerance)) {
            return;
        }

        if ($tolerance < 0 or $tolerance > 100) {
            return;
        }

        return (int) $tolerance;
    }

    /**
     * Resolve the feather.
     *
     * @param string $feather The raw feather.
     *
     * @return int|null The resolved feather.
     */
    public function getFeather($feather)
    {
        if (!is_numeric($feather)) {
            return;
        }

        return (int) $feather;
    }
}
