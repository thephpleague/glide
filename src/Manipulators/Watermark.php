<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use League\Flysystem\FilesystemException as FilesystemV2Exception;
use League\Flysystem\FilesystemOperator;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Manipulators\Helpers\Dimension;

class Watermark extends BaseManipulator
{
    /**
     * The watermarks file system.
     */
    protected ?FilesystemOperator $watermarks = null;

    /**
     * The watermarks path prefix.
     */
    protected string $watermarksPathPrefix = '';

    /**
     * Create Watermark instance.
     *
     * @param FilesystemOperator $watermarks The watermarks file system.
     */
    public function __construct(?FilesystemOperator $watermarks = null, string $watermarksPathPrefix = '')
    {
        $this->setWatermarks($watermarks);
        $this->setWatermarksPathPrefix($watermarksPathPrefix);
    }

    /**
     * Set the watermarks file system.
     *
     * @param FilesystemOperator $watermarks The watermarks file system.
     */
    public function setWatermarks(?FilesystemOperator $watermarks = null): void
    {
        $this->watermarks = $watermarks;
    }

    /**
     * Get the watermarks file system.
     *
     * @return FilesystemOperator|null The watermarks file system.
     */
    public function getWatermarks(): ?FilesystemOperator
    {
        return $this->watermarks;
    }

    /**
     * Set the watermarks path prefix.
     *
     * @param string $watermarksPathPrefix The watermarks path prefix.
     */
    public function setWatermarksPathPrefix(string $watermarksPathPrefix = ''): void
    {
        $this->watermarksPathPrefix = trim($watermarksPathPrefix, '/');
    }

    /**
     * Get the watermarks path prefix.
     *
     * @return string The watermarks path prefix.
     */
    public function getWatermarksPathPrefix(): string
    {
        return $this->watermarksPathPrefix;
    }

    /**
     * Perform watermark image manipulation.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface The manipulated image.
     */
    public function run(ImageInterface $image): ImageInterface
    {
        $watermark = $this->getImage($image);

        if (null === $watermark) {
            return $image;
        }

        $markw = $this->getDimension($image, 'markw');
        $markh = $this->getDimension($image, 'markh');
        $markx = $this->getDimension($image, 'markx');
        $marky = $this->getDimension($image, 'marky');
        $markpad = $this->getDimension($image, 'markpad');
        $markfit = $this->getFit();
        $markpos = $this->getPosition();
        $markalpha = $this->getAlpha();

        if (null !== $markpad) {
            $markx = $marky = $markpad;
        }

        $size = new Size();
        $size->setParams([
            'w' => $markw,
            'h' => $markh,
            'fit' => $markfit,
        ]);
        $watermark = $size->run($watermark);

        return $image->place($watermark, $markpos, intval($markx), intval($marky), $markalpha);
    }

    /**
     * Get the watermark image.
     *
     * @param ImageInterface $image The source image.
     *
     * @return ImageInterface|null The watermark image.
     */
    public function getImage(ImageInterface $image): ?ImageInterface
    {
        if (null === $this->watermarks) {
            return null;
        }

        $path = (string) $this->getParam('mark');

        if ('' === $path) {
            return null;
        }

        if ($this->watermarksPathPrefix) {
            $path = $this->watermarksPathPrefix.'/'.$path;
        }

        $mark = null;
        try {
            if ($this->watermarks->fileExists($path)) {
                $source = $this->watermarks->read($path);

                $mark = $image->driver()->handleInput($source);
            }
        } catch (FilesystemV2Exception $exception) {
            throw new FilesystemException('Could not read the image `'.$path.'`.');
        }

        if ($mark instanceof ImageInterface) {
            return $mark;
        }

        return null;
    }

    /**
     * Get a dimension.
     *
     * @param ImageInterface $image The source image.
     * @param string         $field The requested field.
     *
     * @return float|null The dimension.
     */
    public function getDimension(ImageInterface $image, string $field): ?float
    {
        $dim = $this->getParam($field);

        if ($dim) {
            return (new Dimension($image, $this->getDpr()))->get((string) $dim);
        }

        return null;
    }

    /**
     * Resolve the device pixel ratio.
     *
     * @return float The device pixel ratio.
     */
    public function getDpr(): float
    {
        $dpr = $this->getParam('dpr');

        if (!is_numeric($dpr)) {
            return 1.0;
        }

        if ($dpr < 0 || $dpr > 8) {
            return 1.0;
        }

        return (float) $dpr;
    }

    /**
     * Get the fit.
     *
     * @return string|null The fit.
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

        $markfit = $this->getParam('markfit');

        if (in_array($markfit, $fitMethods, true)) {
            return $markfit;
        }

        return null;
    }

    /**
     * Get the position.
     *
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

        $markpos = $this->getParam('markpos');

        if (in_array($markpos, $positions, true)) {
            return $markpos;
        }

        return 'bottom-right';
    }

    /**
     * Get the alpha channel.
     *
     * @return int The alpha.
     */
    public function getAlpha(): int
    {
        $markalpha = $this->getParam('markalpha');

        if (!is_numeric($markalpha)) {
            return 100;
        }

        if ($markalpha < 0 || $markalpha > 100) {
            return 100;
        }

        return (int) $markalpha;
    }
}
