<?php

namespace League\Glide;

use InvalidArgumentException;
use League\Flysystem\FilesystemException as FSException;
use League\Flysystem\FilesystemOperator;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Responses\ResponseFactoryInterface;

class Server
{
    /**
     * Source file system.
     * @var FilesystemOperator
     */
    protected $source;

    /**
     * Source path prefix.
     * @var string
     */
    protected $sourcePathPrefix = '';

    /**
     * Cache file system.
     * @var FilesystemOperator
     */
    protected $cache;

    /**
     * Cache path prefix.
     * @var string
     */
    protected $cachePathPrefix = '';

    /**
     * Whether to group cache in folders.
     * @var bool
     */
    protected $groupCacheInFolders = true;

    /**
     * Whether to cache with file extensions.
     * @var bool
     */
    protected $cacheWithFileExtensions = false;

    /**
     * Image manipulation API.
     * @var ApiInterface
     */
    protected $api;

    /**
     * Response factory.
     * @var ResponseFactoryInterface|null
     */
    protected $responseFactory;

    /**
     * Base URL.
     * @var string
     */
    protected $baseUrl = '';

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
     * @param FilesystemOperator $source Source file system.
     * @param FilesystemOperator $cache  Cache file system.
     * @param ApiInterface        $api    Image manipulation API.
     */
    public function __construct(FilesystemOperator $source, FilesystemOperator $cache, ApiInterface $api)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setApi($api);
    }

    /**
     * Set source file system.
     * @param FilesystemOperator $source Source file system.
     * @return void
     */
    public function setSource(FilesystemOperator $source)
    {
        $this->source = $source;
    }

    /**
     * Get source file system.
     * @return FilesystemOperator Source file system.
     */
    public function getSource(): FilesystemOperator
    {
        return $this->source;
    }

    /**
     * Set source path prefix.
     * @param string $sourcePathPrefix Source path prefix.
     * @return void
     */
    public function setSourcePathPrefix(string $sourcePathPrefix)
    {
        $this->sourcePathPrefix = trim($sourcePathPrefix, '/');
    }

    /**
     * Get source path prefix.
     * @return string Source path prefix.
     */
    public function getSourcePathPrefix(): string
    {
        return $this->sourcePathPrefix;
    }

    /**
     * Get source path.
     * @param  string                $path Image path.
     * @return string                The source path.
     * @throws FileNotFoundException
     */
    public function getSourcePath(string $path): string
    {
        $path = trim($path, '/');

        $baseUrl = $this->baseUrl . '/';

        if (substr($path, 0, strlen($baseUrl)) === $baseUrl) {
            $path = trim(substr($path, strlen($baseUrl)), '/');
        }

        if ($path === '') {
            throw new FileNotFoundException('Image path missing.');
        }

        if ($this->sourcePathPrefix) {
            $path = $this->sourcePathPrefix . '/' . $path;
        }

        return rawurldecode($path);
    }

    /**
     * Check if a source file exists.
     * @param  string $path Image path.
     * @return bool   Whether the source file exists.
     */
    public function sourceFileExists(string $path): bool
    {
        return $this->source->fileExists($this->getSourcePath($path));
    }

    /**
     * Set base URL.
     * @param string $baseUrl Base URL.
     * @return void
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = trim($baseUrl, '/');
    }

    /**
     * Get base URL.
     * @return string Base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Set cache file system.
     * @param FilesystemOperator $cache Cache file system.
     * @return void
     */
    public function setCache(FilesystemOperator $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get cache file system.
     * @return FilesystemOperator Cache file system.
     */
    public function getCache(): FilesystemOperator
    {
        return $this->cache;
    }

    /**
     * Set cache path prefix.
     * @param string $cachePathPrefix Cache path prefix.
     * @return void
     */
    public function setCachePathPrefix(string $cachePathPrefix)
    {
        $this->cachePathPrefix = trim($cachePathPrefix, '/');
    }

    /**
     * Get cache path prefix.
     * @return string Cache path prefix.
     */
    public function getCachePathPrefix(): string
    {
        return $this->cachePathPrefix;
    }

    /**
     * Set the group cache in folders setting.
     * @param bool $groupCacheInFolders Whether to group cache in folders.
     * @return void
     */
    public function setGroupCacheInFolders(bool $groupCacheInFolders)
    {
        $this->groupCacheInFolders = $groupCacheInFolders;
    }

    /**
     * Get the group cache in folders setting.
     * @return bool Whether to group cache in folders.
     */
    public function getGroupCacheInFolders(): bool
    {
        return $this->groupCacheInFolders;
    }

    /**
     * Set the cache with file extensions setting.
     * @param bool $cacheWithFileExtensions Whether to cache with file extensions.
     * @return void
     */
    public function setCacheWithFileExtensions(bool $cacheWithFileExtensions)
    {
        $this->cacheWithFileExtensions = $cacheWithFileExtensions;
    }

    /**
     * Get the cache with file extensions setting.
     * @return bool Whether to cache with file extensions.
     */
    public function getCacheWithFileExtensions(): bool
    {
        return $this->cacheWithFileExtensions;
    }

    /**
     * Get cache path.
     * @param  string $path   Image path.
     * @param  array  $params Image manipulation params.
     * @return string Cache path.
     */
    public function getCachePath(string $path, array $params = []): string
    {
        $sourcePath = $this->getSourcePath($path);

        if ($this->sourcePathPrefix) {
            $sourcePath = substr($sourcePath, strlen($this->sourcePathPrefix) + 1);
        }

        $params = $this->getAllParams($params);
        unset($params['s'], $params['p']);
        ksort($params);

        $md5 = md5($sourcePath . '?' . http_build_query($params));

        $cachedPath = $this->groupCacheInFolders ? $sourcePath . '/' . $md5 : $md5;

        if ($this->cachePathPrefix) {
            $cachedPath = $this->cachePathPrefix . '/' . $cachedPath;
        }

        if ($this->cacheWithFileExtensions) {
            $ext = (isset($params['fm']) ? $params['fm'] : pathinfo($path)['extension']);
            $ext = ($ext === 'pjpg') ? 'jpg' : $ext;
            $cachedPath .= '.' . $ext;
        }

        return $cachedPath;
    }

    /**
     * Check if a cache file exists.
     * @param  string $path   Image path.
     * @param  array  $params Image manipulation params.
     * @return bool   Whether the cache file exists.
     */
    public function cacheFileExists(string $path, array $params): bool
    {
        return $this->cache->fileExists(
            $this->getCachePath($path, $params)
        );
    }

    /**
     * Delete cached manipulations for an image.
     * @param  string $path Image path.
     * @return void   Whether the delete succeeded.
     */
    public function deleteCache(string $path): void
    {
        if (!$this->groupCacheInFolders) {
            throw new InvalidArgumentException(
                'Deleting cached image manipulations is not possible when grouping cache into folders is disabled.'
            );
        }

        $this->cache->deleteDirectory(
            dirname($this->getCachePath($path))
        );
    }

    /**
     * Set image manipulation API.
     * @param ApiInterface $api Image manipulation API.
     * @return void
     */
    public function setApi(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Get image manipulation API.
     * @return ApiInterface Image manipulation API.
     */
    public function getApi(): ApiInterface
    {
        return $this->api;
    }

    /**
     * Set default image manipulations.
     * @param array $defaults Default image manipulations.
     * @return void
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * Get default image manipulations.
     * @return array Default image manipulations.
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Set preset image manipulations.
     * @param array $presets Preset image manipulations.
     * @return void
     */
    public function setPresets(array $presets)
    {
        $this->presets = $presets;
    }

    /**
     * Get preset image manipulations.
     * @return array Preset image manipulations.
     */
    public function getPresets(): array
    {
        return $this->presets;
    }

    /**
     * Get all image manipulations params, including defaults and presets.
     * @param  array $params Image manipulation params.
     * @return array All image manipulation params.
     */
    public function getAllParams(array $params): array
    {
        $all = $this->defaults;

        if (isset($params['p'])) {
            foreach (explode(',', $params['p']) as $preset) {
                if (isset($this->presets[$preset])) {
                    $all = array_merge($all, $this->presets[$preset]);
                }
            }
        }

        return array_merge($all, $params);
    }

    /**
     * Set response factory.
     * @param ResponseFactoryInterface|null $responseFactory Response factory.
     * @return void
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory = null)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Get response factory.
     * @return ResponseFactoryInterface|null Response factory.
     */
    public function getResponseFactory(): ?ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    /**
     * Generate and return image response.
     * @param  string                   $path   Image path.
     * @param  array                    $params Image manipulation params.
     * @return mixed                    Image response.
     * @throws InvalidArgumentException
     */
    public function getImageResponse(string $path, array $params)
    {
        if (is_null($this->responseFactory)) {
            throw new InvalidArgumentException(
                'Unable to get image response, no response factory defined.'
            );
        }

        $path = $this->makeImage($path, $params);

        return $this->responseFactory->create($this->cache, $path);
    }

    /**
     * Generate and return Base64 encoded image.
     * @param  string              $path   Image path.
     * @param  array               $params Image manipulation params.
     * @return string              Base64 encoded image.
     * @throws FilesystemException
     */
    public function getImageAsBase64(string $path, array $params): string
    {
        $path = $this->makeImage($path, $params);

        try {
            $source = $this->cache->read($path);
        } catch (FSException $e) {
            throw new FilesystemException(
                'Could not read the image `' . $path . '`.',
                0,
                $e
            );
        }

        return 'data:' . $this->cache->mimetype($path) . ';base64,' . base64_encode($source);
    }

    /**
     * Generate and output image.
     * @param  string                   $path   Image path.
     * @param  array                    $params Image manipulation params.
     * @throws InvalidArgumentException
     * @return void
     */
    public function outputImage(string $path, array $params)
    {
        $path = $this->makeImage($path, $params);

        header('Content-Type:' . $this->cache->mimeType($path));
        header('Content-Length:' . $this->cache->fileSize($path));
        header('Cache-Control:' . 'max-age=31536000, public');
        header('Expires:' . date_create('+1 years')->format('D, d M Y H:i:s') . ' GMT');

        $stream = $this->cache->readStream($path);

        if (ftell($stream) !== 0) {
            rewind($stream);
        }
        fpassthru($stream);
        fclose($stream);
    }

    /**
     * Generate manipulated image.
     * @param  string                $path   Image path.
     * @param  array                 $params Image manipulation params.
     * @return string                Cache path.
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    public function makeImage(string $path, array $params): string
    {
        $sourcePath = $this->getSourcePath($path);
        $cachedPath = $this->getCachePath($path, $params);

        if ($this->cacheFileExists($path, $params) === true) {
            return $cachedPath;
        }

        if ($this->sourceFileExists($path) === false) {
            throw new FileNotFoundException(
                'Could not find the image `' . $sourcePath . '`.'
            );
        }

        try {
            $source = $this->source->read(
                $sourcePath
            );
        } catch (FSException $e) {
            throw new FilesystemException(
                'Could not read the image `' . $sourcePath . '`.',
                0,
                $e
            );
        }

        // We need to write the image to the local disk before
        // doing any manipulations. This is because EXIF data
        // can only be read from an actual file.
        $tmp = tempnam(sys_get_temp_dir(), 'Glide');

        if (file_put_contents($tmp, $source) === false) {
            throw new FilesystemException(
                'Unable to write temp file for `' . $sourcePath . '`.'
            );
        }

        try {
            $this->cache->write(
                $cachedPath,
                $this->api->run($tmp, $this->getAllParams($params))
            );
        } catch (FSException $e) {
            throw new FilesystemException(
                'Could not write the image `' . $cachedPath . '`',
                0,
                $e
            );
        } finally {
            unlink($tmp);
        }

        return $cachedPath;
    }
}
