<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Image;
use League\Flysystem\FilesystemInterface;
use League\Glide\Manipulators\Helpers\Dimension;

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
     * @param  Image $image  The source image.
     * @param  array $params The manipulation params.
     * @return Image The manipulated image.
     */
    public function run(Image $image, array $params)
    {
        if ($watermark = $this->getImage($image, $params)) {
            $markw = $this->getDimension($image, $params, 'markw');
            $markh = $this->getDimension($image, $params, 'markh');
            $markx = $this->getDimension($image, $params, 'markx');
            $marky = $this->getDimension($image, $params, 'marky');
            $markpad = $this->getDimension($image, $params, 'markpad');
            $markfit = $this->getFit($params);
            $markpos = $this->getPosition($params);

            if ($markpad) {
                $markx = $marky = $markpad;
            }

            $size = new Size();
            $watermark = $size->run($watermark, [
                'w' => $markw,
                'h' => $markh,
                'fit' => $markfit,
            ]);

            $image->insert($watermark, $markpos, intval($markx), intval($marky));
        }

        return $image;
    }

    /**
     * Get the watermark image.
     * @param  Image      $image  The source image.
     * @param  array      $params The manipulation params.
     * @return Image|null The watermark image.
     */
    public function getImage(Image $image, $params)
    {
        if (is_null($this->watermarks)) {
            return;
        }

        if (!isset($params['mark'])) {
            return;
        }

        if (!is_string($params['mark'])) {
            return;
        }

        if ($params['mark'] === '') {
            return;
        }

        $path = $params['mark'];

        if ($this->watermarksPathPrefix) {
            $path = $this->watermarksPathPrefix.'/'.$path;
        }

        if ($this->watermarks->has($path)) {
            $source = $this->watermarks->read($path);

            if ($source === false) {
                throw new FilesystemException(
                    'Could not read the image `'.$path.'`.'
                );
            }

            return $image->getDriver()->init($source);
        }

        return;
    }

    /**
     * Get a dimension.
     * @param  Image       $image  The source image.
     * @param  array       $params The manipulation params.
     * @param  string      $field  The requested field.
     * @return double|null The dimension.
     */
    public function getDimension(Image $image, array $params, $field)
    {
        if (isset($params[$field])) {
            $dpr = $this->getDpr($params);

            return (new Dimension($image, $dpr))->get($params[$field]);
        }
    }

    /**
     * Resolve the device pixel ratio.
     * @param  array  $params The manipulation params.
     * @return double The device pixel ratio.
     */
    public function getDpr($params)
    {
        if (!isset($params['dpr'])) {
            return 1.0;
        }

        if (!is_numeric($params['dpr'])) {
            return 1.0;
        }

        if ($params['dpr'] < 0 or $params['dpr'] > 8) {
            return 1.0;
        }

        return (double) $params['dpr'];
    }

    /**
     * Get the fit.
     * @param  array  $params The manipulation params.
     * @return string The fit.
     */
    public function getFit(array $params)
    {
        if (!isset($params['markfit'])) {
            return;
        }

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

        if (!in_array($params['markfit'], $fitMethods, true)) {
            return;
        }

        return $params['markfit'];
    }

    /**
     * Get the position.
     * @param  array  $params The manipulation params.
     * @return string The position.
     */
    public function getPosition(array $params)
    {
        if (!isset($params['markpos'])) {
            return 'bottom-right';
        }

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

        if (!in_array($params['markpos'], $positions, true)) {
            return 'bottom-right';
        }

        return $params['markpos'];
    }
}
