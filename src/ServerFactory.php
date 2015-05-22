<?php

namespace League\Glide;

use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Glide\Api\ApiFactory;

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
            $this->getApi()
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

        if (is_null($cache)) {
            return new Filesystem(new Local(sys_get_temp_dir()));
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

    /**
     * Get the image manipulation API.
     * @return Api The image manipulation API.
     */
    public function getApi()
    {
        return ApiFactory::create($this->config);
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
