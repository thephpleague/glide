<?php

namespace League\Glide;

use Intervention\Image\ImageManager;
use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Glide\Api\Api;
use League\Glide\Manipulators\Background;
use League\Glide\Manipulators\Blur;
use League\Glide\Manipulators\Border;
use League\Glide\Manipulators\Brightness;
use League\Glide\Manipulators\Contrast;
use League\Glide\Manipulators\Crop;
use League\Glide\Manipulators\Encode;
use League\Glide\Manipulators\Filter;
use League\Glide\Manipulators\Gamma;
use League\Glide\Manipulators\Orientation;
use League\Glide\Manipulators\Pixelate;
use League\Glide\Manipulators\Sharpen;
use League\Glide\Manipulators\Size;
use League\Glide\Manipulators\Watermark;
use League\Glide\Responses\ResponseFactoryInterface;

class ServerFactory
{
    /**
     * Configuration parameters.
     * @var array
     */
    protected $config;

    /**
     * Create server factory instance.
     * @param array $config Configuration parameters.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Create server instance.
     * @return Server The configured Glide server.
     */
    public function getServer()
    {
        $server = new Server(
            $this->getSource(),
            $this->getCache(),
            $this->getApi(),
            $this->getResponseFactory()
        );

        $server->setSourcePathPrefix($this->getSourcePathPrefix());
        $server->setCachePathPrefix($this->getCachePathPrefix());
        $server->setBaseUrl($this->getBaseUrl());

        return $server;
    }

    /**
     * Get the source file system.
     * @return FilesystemInterface The source file system.
     */
    public function getSource()
    {
        $source = null;

        if (isset($this->config['source'])) {
            $source = $this->config['source'];
        }

        if (is_string($source)) {
            return new Filesystem(new Local($source));
        }

        if ($source instanceof FilesystemInterface) {
            return $source;
        }

        throw new InvalidArgumentException('Invalid `source` parameter.');
    }

    /**
     * Get the watermarks file system.
     * @return FilesystemInterface The watermarks file system.
     */
    public function getWatermarks()
    {
        $watermarks = null;

        if (isset($this->config['watermarks'])) {
            $watermarks = $this->config['watermarks'];
        }

        if (is_null($watermarks)) {
            return;
        }

        if (is_string($watermarks)) {
            return new Filesystem(new Local($watermarks));
        }

        if ($watermarks instanceof FilesystemInterface) {
            return $watermarks;
        }

        throw new InvalidArgumentException('Invalid `watermarks` parameter.');
    }

    /**
     * Get the cache file system.
     * @return FilesystemInterface The cache file system.
     */
    public function getCache()
    {
        $cache = null;

        if (isset($this->config['cache'])) {
            $cache = $this->config['cache'];
        }

        if (is_string($cache)) {
            return new Filesystem(new Local($cache));
        }

        if ($cache instanceof FilesystemInterface) {
            return $cache;
        }

        throw new InvalidArgumentException('Invalid `cache` parameter.');
    }

    /**
     * Get the base URL.
     * @return string The base URL.
     */
    public function getBaseUrl()
    {
        $baseUrl = '';

        if (isset($this->config['base_url'])) {
            $baseUrl = $this->config['base_url'];
        }

        return $baseUrl;
    }

    public function getSourcePathPrefix()
    {
        $sourcePathPrefix = '';

        if (isset($this->config['source_path_prefix'])) {
            $sourcePathPrefix = $this->config['source_path_prefix'];
        }

        return $sourcePathPrefix;
    }

    public function getCachePathPrefix()
    {
        $cachePathPrefix = '';

        if (isset($this->config['cache_path_prefix'])) {
            $cachePathPrefix = $this->config['cache_path_prefix'];
        }

        return $cachePathPrefix;
    }

    public function getWatermarksPathPrefix()
    {
        $watermarksPathPrefix = '';

        if (isset($this->config['watermarks_path_prefix'])) {
            $watermarksPathPrefix = $this->config['watermarks_path_prefix'];
        }

        return $watermarksPathPrefix;
    }

    /**
     * Get the image manipulation API.
     * @return Api The image manipulation API.
     */
    public function getApi()
    {
        return new Api(
            $this->getImageManager(),
            $this->getManipulators()
        );
    }

    /**
     * Get the image manager.
     * @return ImageManager Intervention image manager.
     */
    public function getImageManager()
    {
        $driver = 'gd';

        if (isset($this->config['driver'])) {
            $driver = $this->config['driver'];
        }

        return new ImageManager([
            'driver' => $driver,
        ]);
    }

    /**
     * Get the default manipulators.
     * @return array Collection of manipulators.
     */
    public function getManipulators()
    {
        return [
            new Orientation(),
            new Crop(),
            new Size($this->getMaxImageSize()),
            new Brightness(),
            new Contrast(),
            new Gamma(),
            new Sharpen(),
            new Filter(),
            new Blur(),
            new Pixelate(),
            new Watermark($this->getWatermarks(), $this->getWatermarksPathPrefix()),
            new Background(),
            new Border(),
            new Encode(),
        ];
    }

    /**
     * Get the maximum image size in pixels.
     * @return int|null Maximum image size in pixels.
     */
    public function getMaxImageSize()
    {
        $maxImageSize = null;

        if (isset($this->config['max_image_size'])) {
            $maxImageSize = $this->config['max_image_size'];
        }

        return $maxImageSize;
    }

    /**
     * Get the response factory.
     * @return ResponseFactoryInterface The response factory.
     */
    public function getResponseFactory()
    {
        $responseFactory = null;

        if (isset($this->config['response'])) {
            $responseFactory = $this->config['response'];
        }

        if ($responseFactory instanceof ResponseFactoryInterface) {
            return $responseFactory;
        }

        throw new InvalidArgumentException('Invalid `response` parameter.');
    }

    /**
     * Create server instance.
     * @param  array  $config Configuration parameters.
     * @return Server The configured server.
     */
    public static function create(array $config = [])
    {
        return (new self($config))->getServer();
    }
}
