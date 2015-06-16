<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;

class Watermark implements ManipulatorInterface
{
    /**
     * The watermarks file system.
     * @var FilesystemInterface|null
     */
    protected $watermarks;

    /**
     * The watermarks path prefix.
     * @var string|null
     */
    protected $watermarksPathPrefix;

    /**
     * Create Watermark instance.
     * @param FilesystemInterface $watermarks The watermarks file system.
     */
    public function __construct(FilesystemInterface $watermarks = null, $watermarksPathPrefix = null)
    {
        $this->setWatermarks($watermarks);
        $this->setWatermarksPathPrefix($watermarksPathPrefix);
    }

    /**
     * Set the watermarks file system.
     * @param FilesystemInterface $watermarks The watermarks file system.
     */
    public function setWatermarks(FilesystemInterface $watermarks = null)
    {
        $this->watermarks = $watermarks;
    }

    /**
     * Get the watermarks file system.
     * @return FilesystemInterface The watermarks file system.
     */
    public function getWatermarks()
    {
        return $this->watermarks;
    }

    /**
     * Set the watermarks path prefix.
     * @param string $watermarksPathPrefix The watermarks path prefix.
     */
    public function setWatermarksPathPrefix($watermarksPathPrefix = null)
    {
        $this->watermarksPathPrefix = trim($watermarksPathPrefix, '/');
    }

    /**
     * Get the watermarks path prefix.
     * @return string The watermarks path prefix.
     */
    public function getWatermarksPathPrefix()
    {
        return $this->watermarksPathPrefix;
    }

    /**
     * Perform watermark image manipulation.
     * @param  Request $request The request object.
     * @param  Image   $image   The source image.
     * @return Image   The manipulated image.
     */
    public function run(Request $request, Image $image)
    {
        if ($watermark = $this->getWatermark($image, $request->get('mark'))) {
            $markw = $this->resolveDimension($request->get('markw'));
            $markh = $this->resolveDimension($request->get('markh'));
            $markx = $this->resolveDimension($request->get('markx'));
            $marky = $this->resolveDimension($request->get('marky'));
            $markpos = $this->getPosition($request->get('markpos'));

            if ($markw or $markh) {
                $watermark->resize($markw, $markh, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $image->insert($watermark, $markpos, $markx, $marky);
        }

        return $image;
    }

    /**
     * Resolve watermark image.
     * @param  Image      $image The source image.
     * @param  string     $mark  The image path.
     * @return Image|bool The resolved watermark image.
     */
    public function getWatermark(Image $image, $path)
    {
        if (is_null($this->watermarks)) {
            return false;
        }

        if (!is_string($path) or $path === '') {
            return false;
        }

        if ($this->watermarksPathPrefix) {
            $path = $this->watermarksPathPrefix.'/'.$path;
        }

        if ($this->watermarks->has($path)) {
            return $image->getDriver()->init($this->watermarks->read($path));
        }

        return false;
    }

    /**
     * Resolve a watermark dimension.
     * @param  string $dimension The watermark dimension.
     * @return int    The resolved watermark dimension.
     */
    public function resolveDimension($dimension)
    {
        if (is_null($dimension)) {
            return;
        }

        if (!is_numeric($dimension)) {
            return;
        }

        return (int) $dimension;
    }

    /**
     * Resolve the watermark position.
     * @param  string $markpos The watermark position.
     * @return string The resolved watermark position.
     */
    public function getPosition($markpos)
    {
        if (is_null($markpos)) {
            return 'bottom-right';
        }

        if (!in_array((string) $markpos, ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'], true)) {
            return 'bottom-right';
        }

        return $markpos;
    }
}
