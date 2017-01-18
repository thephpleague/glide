<?php

namespace League\Glide;

use League\Flysystem\FilesystemInterface;
use League\Glide\Api\ApiInterface;
use Symfony\Component\HttpFoundation\Request;

class Server
{
    /**
     * Image manipulation API.
     * @var ApiInterface
     */
    protected $api;

    /**
     * Source file system.
     * @var FilesystemInterface
     */
    protected $source;

    /**
     * Source folder.
     * @var string
     */
    protected $sourceFolder;

    /**
     * Cache file system.
     * @var FilesystemInterface
     */
    protected $cache;

    /**
     * Cache folder.
     * @var string
     */
    protected $cacheFolder;

    /**
     * Base URL.
     * @var string
     */
    protected $baseUrl;

    /**
     * Cache URL.
     * @var string
     */
    protected $cacheUrl;

    /**
     * Sign key.
     * @var string
     */
    protected $signKey;

    /**
     * Default image manipulations.
     * @var array
     */
    protected $defaults = [];

    /**
     * Preset image manipulations.
     * @var array
     */
    protected $presets = [];

    /**
     * Create Server instance.
     * @param ApiInterface        $api    Image manipulation API.
     * @param FilesystemInterface $source Source file system.
     * @param FilesystemInterface $cache  Cache file system.
     */
    public function __construct(ApiInterface $api, FilesystemInterface $source, FilesystemInterface $cache)
    {
        $this->setApi($api);
        $this->setSource($source);
        $this->setCache($cache);
    }

    /**
     * Set source file system.
     * @param FilesystemInterface $source Source file system.
     */
    public function setSource(FilesystemInterface $source)
    {
        $this->source = $source;
    }

    /**
     * Get source file system.
     * @return FilesystemInterface Source file system.
     */
    public function getSource()
    {
        return $this->source;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function getSignKey()
    {
        return $this->signKey;
    }

    /**
     * Set source folder.
     * @param string $sourceFolder Source folder.
     */
    public function setSourceFolder($sourceFolder)
    {
        $this->sourceFolder = trim($sourceFolder, '/');
    }

    /**
     * Get source folder.
     * @return string Source folder.
     */
    public function getSourceFolder()
    {
        return $this->sourceFolder;
    }

    /**
     * Set base URL.
     * @param string $baseUrl Base URL.
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = trim($baseUrl, '/');
    }

    /**
     * Get base URL.
     * @return string Base URL.
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set cache URL.
     * @param string $cacheUrl Cache URL.
     */
    public function setCacheUrl($cacheUrl)
    {
        $this->cacheUrl = trim($cacheUrl, '/');
    }

    /**
     * Get cache URL.
     * @return string Cache URL.
     */
    public function getCacheUrl()
    {
        return $this->cacheUrl;
    }

    /**
     * Set cache file system.
     * @param FilesystemInterface $cache Cache file system.
     */
    public function setCache(FilesystemInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get cache file system.
     * @return FilesystemInterface Cache file system.
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set cache folder.
     * @param string $cacheFolder Cache folder.
     */
    public function setCacheFolder($cacheFolder)
    {
        $this->cacheFolder = trim($cacheFolder, '/');
    }

    /**
     * Get cache folder.
     * @return string Cache folder.
     */
    public function getCacheFolder()
    {
        return $this->cacheFolder;
    }

    /**
     * Set image manipulation API.
     * @param ApiInterface $api Image manipulation API.
     */
    public function setApi(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Get image manipulation API.
     * @return ApiInterface Image manipulation API.
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Set default image manipulations.
     * @param array $defaults Default image manipulations.
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * Get default image manipulations.
     * @return array Default image manipulations.
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Set preset image manipulations.
     * @param array $presets Preset image manipulations.
     */
    public function setPresets(array $presets)
    {
        $this->presets = $presets;
    }

    /**
     * Get preset image manipulations.
     * @return array Preset image manipulations.
     */
    public function getPresets()
    {
        return $this->presets;
    }

    public function image($path, $attributes = [], $signature = null)
    {
        return new Image($this, $path, $attributes, $signature);
    }

    public function createFromRequest(Request $request = null)
    {
        $request = $request ?: Request::createFromGlobals();
        $path = $request->getPathInfo();
        $signature = $request->get('s');

        $info = pathinfo($path);
        $path = $info['dirname'];
        $attributes['fm'] = $info['extension'];
        $filename = $info['filename'];
        $info = pathinfo($path);
        $filename = trim(substr($filename, strlen($info['filename'])), '-');

        foreach (array_chunk(explode('-', $filename), 2) as $attribute) {
            if (isset($attribute[0]) and isset($attribute[1])) {
                $attributes[$attribute[0]] = $attribute[1];
            }
        }

        if (substr(ltrim($path, '/'), 0, strlen($this->baseUrl)) === $this->baseUrl) {
            $path = trim(substr(ltrim($path, '/'), strlen($this->baseUrl)), '/');
        }

        return new Image($this, $path, $attributes, $signature);
    }

    /**
     * Create configured server.
     * @param  array  $config Configuration parameters.
     * @return Server Configured server.
     */
    public static function create(array $config = [])
    {
        return (new ServerFactory($config))->create();
    }
}
