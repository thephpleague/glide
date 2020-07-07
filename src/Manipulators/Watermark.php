<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Flysystem\FilesystemInterface;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Manipulators\Helpers\Dimension;

/**
 * @property string $dpr
 * @property string $mark
 * @property string $markfit
 * @property string $markh
 * @property string $markpad
 * @property string $markpos
 * @property string $markw
 * @property string $markx
 * @property string $marky
 * @property string $markalpha
 */
class Watermark extends BaseManipulator
{
    /**
     * The watermarks file system.
     * @var FilesystemInterface|null
     */
    protected $watermarks;

    /**
     * The watermarks path prefix.
     * @var string
     */
    protected $watermarksPathPrefix;

    /**
     * Create Watermark instance.
     * @param FilesystemInterface $watermarks The watermarks file system.
     * @param string $watermarksPathPrefix
     */
    public function __construct(?FilesystemInterface $watermarks = null, string $watermarksPathPrefix = '')
    {
        $this->setWatermarks($watermarks);
        $this->setWatermarksPathPrefix($watermarksPathPrefix);
    }

    /**
     * Set the watermarks file system.
     * @param FilesystemInterface|null $watermarks The watermarks file system.
     * @return void
     */
    public function setWatermarks(?FilesystemInterface $watermarks = null): void
    {
        $this->watermarks = $watermarks;
    }

    /**
     * Get the watermarks file system.
     * @return FilesystemInterface|null The watermarks file system.
     */
    public function getWatermarks(): ?FilesystemInterface
    {
        return $this->watermarks;
    }

    /**
     * Set the watermarks path prefix.
     * @param string $watermarksPathPrefix The watermarks path prefix.
     * @return void
     */
    public function setWatermarksPathPrefix(string $watermarksPathPrefix = ''): void
    {
        $this->watermarksPathPrefix = trim($watermarksPathPrefix, '/');
    }

    /**
     * Get the watermarks path prefix.
     * @return string The watermarks path prefix.
     */
    public function getWatermarksPathPrefix(): string
    {
        return $this->watermarksPathPrefix;
    }

    /**
     * Perform watermark image manipulation.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image): Image
    {
        if ($watermark = $this->getImage($image)) {
            $markw = $this->getDimension($image, 'markw');
            $markh = $this->getDimension($image, 'markh');
            $markx = $this->getDimension($image, 'markx');
            $marky = $this->getDimension($image, 'marky');
            $markpad = $this->getDimension($image, 'markpad');
            $markfit = $this->getFit();
            $markpos = $this->getPosition();
            $markalpha = $this->getAlpha();

            if ($markpad) {
                $markx = $marky = $markpad;
            }

            $size = new Size();
            $size->setParams([
                'w' => $markw,
                'h' => $markh,
                'fit' => $markfit,
            ]);
            $watermark = $size->run($watermark);

            if ($markalpha < 100) {
                $watermark->opacity($markalpha);
            }

            $image->insert($watermark, $markpos, intval($markx), intval($marky));
        }

        return $image;
    }

    /**
     * Get the watermark image.
     * @param  Image      $image The source image.
     * @return Image|null The watermark image.
     */
    public function getImage(Image $image): ?Image
    {
        if (is_null($this->watermarks)) {
            return null;
        }

        if (!is_string($this->mark)) {
            return null;
        }

        if ($this->mark === '') {
            return null;
        }

        $path = $this->mark;

        if ($this->watermarksPathPrefix) {
            $path = $this->watermarksPathPrefix . '/' . $path;
        }

        if ($this->watermarks->has($path)) {
            $source = $this->watermarks->read($path);

            if ($source === false) {
                throw new FilesystemException(
                    'Could not read the image `' . $path . '`.'
                );
            }

            return $image->getDriver()->init($source);
        }

        return null;
    }

    /**
     * Get a dimension.
     * @param  Image       $image The source image.
     * @param  string      $field The requested field.
     * @return double|null The dimension.
     */
    public function getDimension(Image $image, string $field): ?float
    {
        if ($this->{$field}) {
            return (new Dimension($image, $this->getDpr()))->get((string) $this->{$field});
        }

        return null;
    }

    /**
     * Resolve the device pixel ratio.
     * @return double The device pixel ratio.
     */
    public function getDpr(): float
    {
        if (!is_numeric($this->dpr)) {
            return 1.0;
        }

        if ($this->dpr < 0 or $this->dpr > 8) {
            return 1.0;
        }

        return (double) $this->dpr;
    }

    /**
     * Get the fit.
     * @return null|string The fit.
     */
    public function getFit(): ?string
    {
        $fitMethods = [
            'contain',
            'max',
            'stretch',
            'crop',
            'crop-top-left',
            'crop-top',
            'crop-top-right',
            'crop-left',
            'crop-center',
            'crop-right',
            'crop-bottom-left',
            'crop-bottom',
            'crop-bottom-right',
        ];

        if (in_array($this->markfit, $fitMethods, true)) {
            return $this->markfit;
        }

        return null;
    }

    /**
     * Get the position.
     * @return string The position.
     */
    public function getPosition(): string
    {
        $positions = [
            'top-left',
            'top',
            'top-right',
            'left',
            'center',
            'right',
            'bottom-left',
            'bottom',
            'bottom-right',
        ];

        if (in_array($this->markpos, $positions, true)) {
            return $this->markpos;
        }

        return 'bottom-right';
    }

    /**
     * Get the alpha channel.
     * @return int The alpha.
     */
    public function getAlpha(): int
    {
        if (!is_numeric($this->markalpha)) {
            return 100;
        }

        if ($this->markalpha < 0 or $this->markalpha > 100) {
            return 100;
        }

        return (int) $this->markalpha;
    }
}
