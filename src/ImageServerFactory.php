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

class ImageServerFactory
{
    /**
     * Configuration parameters.
     * @var array
     */
    protected $config;

    /**
     * Create ImageServerFactory instance.
     * @param array $config Configuration parameters.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get configured server.
     * @return ImageServer Configured Glide server.
     */
    public function create()
    {
        $server = new ImageServer(
            $this->getSource(),
            $this->getCache(),
            $this->getApi(),
            $this->getSignKey()
        );

        $server->setSourceFolder($this->getSourceFolder());
        $server->setCacheFolder($this->getCacheFolder());
        $server->setDefaults($this->getDefaults());
        $server->setPresets($this->getPresets());
        $server->setBaseUrl($this->getBaseUrl());
        $server->setCacheUrl($this->getCacheUrl());
        $server->setResponseFactory($this->getResponseFactory());

        return $server;
    }

    /**
     * Get source file system.
     * @return FilesystemInterface Source file system.
     */
    public function getSource()
    {
        if (!isset($this->config['source'])) {
            throw new InvalidArgumentException('A "source" file system must be set.');
        }

        if (is_string($this->config['source'])) {
            return new Filesystem(
                new Local($this->config['source'])
            );
        }

        return $this->config['source'];
    }

    /**
     * Get source folder.
     * @return string|null Source folder.
     */
    public function getSourceFolder()
    {
        if (isset($this->config['source_folder'])) {
            return $this->config['source_folder'];
        }
    }

    /**
     * Get cache file system.
     * @return FilesystemInterface Cache file system.
     */
    public function getCache()
    {
        if (!isset($this->config['cache'])) {
            throw new InvalidArgumentException('A "cache" file system must be set.');
        }

        if (is_string($this->config['cache'])) {
            return new Filesystem(
                new Local($this->config['cache'])
            );
        }

        return $this->config['cache'];
    }

    /**
     * Get cache folder.
     * @return string|null Cache folder.
     */
    public function getCacheFolder()
    {
        if (isset($this->config['cache_folder'])) {
            return $this->config['cache_folder'];
        }
    }

    /**
     * Get watermarks file system.
     * @return FilesystemInterface|null Watermarks file system.
     */
    public function getWatermarks()
    {
        if (!isset($this->config['watermarks'])) {
            return;
        }

        if (is_string($this->config['watermarks'])) {
            return new Filesystem(
                new Local($this->config['watermarks'])
            );
        }

        return $this->config['watermarks'];
    }

    /**
     * Get watermarks folder.
     * @return string|null Watermarks folder.
     */
    public function getWatermarksFolder()
    {
        if (isset($this->config['watermarks_folder'])) {
            return $this->config['watermarks_folder'];
        }
    }

    /**
     * Get image manipulation API.
     * @return Api Image manipulation API.
     */
    public function getApi()
    {
        return new Api(
            $this->getImageManager(),
            $this->getManipulators()
        );
    }

    /**
     * Get Intervention image manager.
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
     * Get image manipulators.
     * @return array Image manipulators.
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
            new Watermark($this->getWatermarks(), $this->getWatermarksFolder()),
            new Background(),
            new Border(),
            new Encode(),
        ];
    }

    /**
     * Get maximum image size.
     * @return int|null Maximum image size.
     */
    public function getMaxImageSize()
    {
        if (isset($this->config['max_image_size'])) {
            return $this->config['max_image_size'];
        }
    }

    /**
     * Get default image manipulations.
     * @return array Default image manipulations.
     */
    public function getDefaults()
    {
        if (isset($this->config['defaults'])) {
            return $this->config['defaults'];
        }

        return [];
    }

    /**
     * Get preset image manipulations.
     * @return array Preset image manipulations.
     */
    public function getPresets()
    {
        if (isset($this->config['presets'])) {
            return $this->config['presets'];
        }

        return [];
    }

    /**
     * Get sign key.
     * @return string|null Sign key.
     */
    public function getSignKey()
    {
        if (!isset($this->config['sign_key'])) {
            throw new InvalidArgumentException('A signing key must be set.');
        }

        if (isset($this->config['sign_key'])) {
            return $this->config['sign_key'];
        }
    }

    /**
     * Get cache URL.
     * @return string|null Cache URl.
     */
    public function getCacheUrl()
    {
        if (isset($this->config['cache_url'])) {
            return $this->config['cache_url'];
        }
    }

    /**
     * Get base URL.
     * @return string|null Base URL.
     */
    public function getBaseUrl()
    {
        if (isset($this->config['base_url'])) {
            return $this->config['base_url'];
        }
    }

    /**
     * Get response factory.
     * @return ResponseFactoryInterface|null Response factory.
     */
    public function getResponseFactory()
    {
        if (isset($this->config['response'])) {
            return $this->config['response'];
        }
    }
}
