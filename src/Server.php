<?php

namespace League\Glide;

use InvalidArgumentException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Http\NotFoundException;
use League\Glide\Http\RequestFactory;
use League\Glide\Http\ResponseFactory;
use League\Glide\Http\UncachedResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Server
{
    /**
     * The source file system.
     * @var FilesystemInterface
     */
    protected $source;

    /**
     * The source path prefix.
     * @var string
     */
    protected $sourcePathPrefix;

    /**
     * The cache file system.
     * @var FilesystemInterface|null
     */
    protected $cache;

    /**
     * The cache path prefix.
     * @var string
     */
    protected $cachePathPrefix;

    /**
     * The image manipulation API.
     * @var ApiInterface
     */
    protected $api;

    /**
     * The base URL to exclude.
     * @var string
     */
    protected $baseUrl;

    /**
     * The binary string of the manipulated source image.
     *
     * @var string
     */
    protected $manipulatedSource;

    /**
     * Create Server instance.
     * @param FilesystemInterface      $source The source file system.
     * @param FilesystemInterface|null $cache  The cache file system.
     * @param ApiInterface             $api    The image manipulation API.
     */
    public function __construct(FilesystemInterface $source, $cache, ApiInterface $api)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setApi($api);
    }

    /**
     * Set the source file system.
     * @param FilesystemInterface $source The source file system.
     */
    public function setSource(FilesystemInterface $source)
    {
        $this->source = $source;
    }

    /**
     * Get the source file system.
     * @return FilesystemInterface The source file system.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source path prefix.
     * @param string $sourcePathPrefix The source path prefix.
     */
    public function setSourcePathPrefix($sourcePathPrefix)
    {
        $this->sourcePathPrefix = trim($sourcePathPrefix, '/');
    }

    /**
     * Get the source path prefix.
     * @return string The source path prefix.
     */
    public function getSourcePathPrefix()
    {
        return $this->sourcePathPrefix;
    }

    /**
     * Get the source path.
     * @param  mixed
     * @return string            The source path.
     * @throws NotFoundException
     */
    public function getSourcePath()
    {
        $request = $this->resolveRequestObject(func_get_args());

        $path = trim($request->getPathInfo(), '/');

        if (substr($path, 0, strlen($this->baseUrl)) === $this->baseUrl) {
            $path = trim(substr($path, strlen($this->baseUrl)), '/');
        }

        if ($path === '') {
            throw new NotFoundException('Image path missing.');
        }

        if ($this->sourcePathPrefix) {
            $path = $this->sourcePathPrefix.'/'.$path;
        }

        return rawurldecode($path);
    }

    /**
     * Check if a source file exists.
     * @param  mixed
     * @return bool
     */
    public function sourceFileExists()
    {
        $request = $this->resolveRequestObject(func_get_args());

        return $this->source->has($this->getSourcePath($request));
    }

    /**
     * Set the cache file system.
     * @param FilesystemInterface|null $cache The cache file system.
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get the cache file system.
     * @return FilesystemInterface|null The cache file system.
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache path prefix.
     * @param string $cachePathPrefix The cache path prefix.
     */
    public function setCachePathPrefix($cachePathPrefix)
    {
        $this->cachePathPrefix = trim($cachePathPrefix, '/');
    }

    /**
     * Get the cache path prefix.
     * @return string The cache path prefix.
     */
    public function getCachePathPrefix()
    {
        return $this->cachePathPrefix;
    }

    /**
     * Get the cache path.
     * @param  mixed
     * @return string The cache path.
     */
    public function getCachePath()
    {
        $request = $this->resolveRequestObject(func_get_args());

        $path = md5($this->getSourcePath($request).'?'.http_build_query($request->query->all()));

        if ($this->cachePathPrefix) {
            $path = $this->cachePathPrefix.'/'.$path;
        }

        return $path;
    }

    /**
     * Check if a cache file exists.
     * @param  mixed
     * @return bool
     */
    public function cacheFileExists()
    {
        if ($this->cache === null) {
            return false;
        }

        $request = $this->resolveRequestObject(func_get_args());

        return $this->cache->has($this->getCachePath($request));
    }

    /**
     * Set the image manipulation API.
     * @param ApiInterface $api The image manipulation API.
     */
    public function setApi(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Get the image manipulation API.
     * @return ApiInterface The image manipulation API.
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Set the base URL.
     * @param string $baseUrl The base URL.
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = trim($baseUrl, '/');
    }

    /**
     * Get the base URL.
     * @return string The base URL.
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Generate and output manipulated image.
     * @param  mixed
     * @return Request The request object.
     */
    public function outputImage()
    {
        $request = $this->resolveRequestObject(func_get_args());

        $this->makeImage($request);

        if ($this->cache === null) {
            $sourceLastModified = $this->source->getTimestamp('');

            UncachedResponseFactory::create($this->manipulatedSource, $request, $sourceLastModified)->send();

            return $request;
        }

        ResponseFactory::create($this->cache, $request, $this->getCachePath($request))->send();

        return $request;
    }

    /**
     * Generate and return response object of manipulated image.
     * @param  mixed
     * @return StreamedResponse The response object.
     */
    public function getImageResponse()
    {
        $request = $this->resolveRequestObject(func_get_args());

        $this->makeImage($request);

        return ResponseFactory::create($this->cache, $request, $this->getCachePath($request));
    }

    /**
     * Generate manipulated image.
     * @return Request           The request object.
     * @throws NotFoundException
     */
    public function makeImage()
    {
        $request = $this->resolveRequestObject(func_get_args());

        if ($this->cacheFileExists($request) === true) {
            return $request;
        }

        if ($this->sourceFileExists($request) === false) {
            throw new NotFoundException(
                'Could not find the image `'.$this->getSourcePath($request).'`.'
            );
        }

        $source = $this->source->read(
            $this->getSourcePath($request)
        );

        if ($source === false) {
            throw new FilesystemException(
                'Could not read the image `'.$this->getSourcePath($request).'`.'
            );
        }

        try {
            $written = $this->writeCache($request, $source);
        } catch (FileExistsException $exception) {
            // Cache file failed to write. Fail silently.
            return $request;
        }

        if ($written === false) {
            throw new FilesystemException(
                'Could not write the image `'.$this->getCachePath($request).'`.'
            );
        }

        return $request;
    }

    /**
     * Resolve request object.
     * @param  array   $args Array of supplied arguments.
     * @return Request The request object.
     */
    protected function resolveRequestObject($args)
    {
        if (isset($args[0]) and $args[0] instanceof Request) {
            return $args[0];
        }

        if (isset($args[0]) and is_string($args[0])) {
            $path = $args[0];
            $params = [];

            if (isset($args[1]) and is_array($args[1])) {
                $params = $args[1];
            }

            return RequestFactory::create($path, $params);
        }

        throw new InvalidArgumentException('Not a valid path or Request object.');
    }

    /**
     * Write the cache file if caching is enabled.
     * @param Request $request
     * @param string  $source
     * @return mixed
     */
    private function writeCache(Request $request, $source)
    {
        if ($this->cache === null) {
            $this->manipulatedSource = $this->api->run($request, $source);

            return null;
        }

        return $this->cache->write(
            $this->getCachePath($request),
            $this->api->run($request, $source)
        );
    }
}
