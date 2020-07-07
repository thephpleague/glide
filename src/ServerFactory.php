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
use League\Glide\Manipulators\Flip;
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
     * Create ServerFactory instance.
     * @param array $config Configuration parameters.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get configured server.
     * @return Server Configured Glide server.
     */
    public function getServer(): Server
    {
        $server = new Server(
            $this->getSource(),
            $this->getCache(),
            $this->getApi()
        );

        $server->setSourcePathPrefix((string) $this->getSourcePathPrefix());
        $server->setCachePathPrefix((string) $this->getCachePathPrefix());
        $server->setGroupCacheInFolders($this->getGroupCacheInFolders());
        $server->setCacheWithFileExtensions($this->getCacheWithFileExtensions());
        $server->setDefaults($this->getDefaults());
        $server->setPresets($this->getPresets());
        $server->setBaseUrl((string) $this->getBaseUrl());
        $server->setResponseFactory($this->getResponseFactory());

        return $server;
    }

    /**
     * Get source file system.
     * @return FilesystemInterface Source file system.
     */
    public function getSource(): FilesystemInterface
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
     * Get source path prefix.
     * @return string|null Source path prefix.
     */
    public function getSourcePathPrefix(): ?string
    {
        return $this->config['source_path_prefix'] ?? null;
    }

    /**
     * Get cache file system.
     * @return FilesystemInterface Cache file system.
     */
    public function getCache(): FilesystemInterface
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
     * Get cache path prefix.
     * @return string|null Cache path prefix.
     */
    public function getCachePathPrefix(): ?string
    {
        return $this->config['cache_path_prefix'] ?? null;
    }

    /**
     * Get the group cache in folders setting.
     * @return bool Whether to group cache in folders.
     */
    public function getGroupCacheInFolders(): bool
    {
        if (isset($this->config['group_cache_in_folders'])) {
            return $this->config['group_cache_in_folders'];
        }

        return true;
    }

    /**
     * Get the cache with file extensions setting.
     * @return bool Whether to cache with file extensions.
     */
    public function getCacheWithFileExtensions(): bool
    {
        if (isset($this->config['cache_with_file_extensions'])) {
            return $this->config['cache_with_file_extensions'];
        }

        return false;
    }

    /**
     * Get watermarks file system.
     * @return FilesystemInterface|null Watermarks file system.
     */
    public function getWatermarks(): ?FilesystemInterface
    {
        if (!isset($this->config['watermarks'])) {
            return null;
        }

        if (is_string($this->config['watermarks'])) {
            return new Filesystem(
                new Local($this->config['watermarks'])
            );
        }

        return $this->config['watermarks'];
    }

    /**
     * Get watermarks path prefix.
     * @return string|null Watermarks path prefix.
     */
    public function getWatermarksPathPrefix(): ?string
    {
        return $this->config['watermarks_path_prefix'] ?? null;
    }

    /**
     * Get image manipulation API.
     * @return Api Image manipulation API.
     */
    public function getApi(): Api
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
    public function getImageManager(): ImageManager
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
    public function getManipulators(): array
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
            new Flip(),
            new Blur(),
            new Pixelate(),
            new Watermark($this->getWatermarks(), (string) $this->getWatermarksPathPrefix()),
            new Background(),
            new Border(),
            new Encode(),
        ];
    }

    /**
     * Get maximum image size.
     * @return int|null Maximum image size.
     */
    public function getMaxImageSize(): ?int
    {
        return $this->config['max_image_size'] ?? null;
    }

    /**
     * Get default image manipulations.
     * @return array Default image manipulations.
     */
    public function getDefaults(): array
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
    public function getPresets(): array
    {
        if (isset($this->config['presets'])) {
            return $this->config['presets'];
        }

        return [];
    }

    /**
     * Get base URL.
     * @return string|null Base URL.
     */
    public function getBaseUrl(): ?string
    {
        return $this->config['base_url'] ?? null;
    }

    /**
     * Get response factory.
     * @return ResponseFactoryInterface|null Response factory.
     */
    public function getResponseFactory(): ?ResponseFactoryInterface
    {
        return $this->config['response'] ?? null;
    }

    /**
     * Create configured server.
     * @param  array  $config Configuration parameters.
     * @return Server Configured server.
     */
    public static function create(array $config = []): Server
    {
        return (new self($config))->getServer();
    }
}
