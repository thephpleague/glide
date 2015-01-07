<?php

namespace League\Glide;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use League\Glide\Exceptions\ImageNotFoundException;
use League\Glide\Factories\Request as RequestFactory;
use League\Glide\Interfaces\Api as ApiInterface;
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
     * The cache file system.
     * @var FilesystemInterface
     */
    protected $cache;

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
     * Create Server instance.
     * @param FilesystemInterface $source  The source file system.
     * @param FilesystemInterface $cache   The cache file system.
     * @param ApiInterface        $api     The image manipulation API.
     * @param string              $baseUrl The base URL.
     */
    public function __construct(FilesystemInterface $source, FilesystemInterface $cache, ApiInterface $api, $baseUrl = '')
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setApi($api);
        $this->setBaseUrl($baseUrl);
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
     * Set the cache file system.
     * @param FilesystemInterface $cache The cache file system.
     */
    public function setCache(FilesystemInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get the cache file system.
     * @return FilesystemInterface The cache file system.
     */
    public function getCache()
    {
        return $this->cache;
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
        $this->baseUrl = $baseUrl;
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
     * Resolve request object.
     * @param  array   $args Array of supplied arguments.
     * @return Request The request object.
     */
    public function resolveRequestObject($args)
    {
        if (isset($args[0]) and $args[0] instanceof Request) {
            return $args[0];
        }

        if (isset($args[0]) and is_string($args[0])) {
            $filename = $args[0];
            $params = [];

            if (isset($args[1]) and is_array($args[1])) {
                $params = $args[1];
            }

            return RequestFactory::create($filename, $params);
        }

        throw new InvalidArgumentException('Not a valid filename or Request object.');
    }

    /**
     * Get the source filename.
     * @param  mixed
     * @return string The source filename.
     */
    public function getSourceFilename()
    {
        $request = $this->resolveRequestObject(func_get_args());

        $baseUrl = trim($this->baseUrl, '/');
        $path = trim($request->getPathInfo(), '/');

        if (substr($path, 0, strlen($baseUrl)) === $baseUrl) {
            $path = trim(substr($path, strlen($baseUrl)), '/');
        }

        return $path;
    }

    /**
     * Get the cache filename.
     * @param  mixed
     * @return string The cache filename.
     */
    public function getCacheFilename()
    {
        $request = $this->resolveRequestObject(func_get_args());

        return md5($this->getSourceFilename($request).'?'.http_build_query($request->query->all()));
    }

    /**
     * Check if a source file exists.
     * @param  mixed
     * @return bool
     */
    public function sourceFileExists()
    {
        $request = $this->resolveRequestObject(func_get_args());

        return $this->source->has($this->getSourceFilename($request));
    }

    /**
     * Check if a cache file exists.
     * @param  mixed
     * @return bool
     */
    public function cacheFileExists()
    {
        $request = $this->resolveRequestObject(func_get_args());

        return $this->cache->has($this->getCacheFilename($request));
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

        $output = new Output($this->cache);
        $output->getResponse($this->getCacheFilename($request))->send();

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

        $output = new Output($this->cache);

        return $output->getResponse($this->getCacheFilename($request));
    }

    /**
     * Generate manipulated image.
     * @return Request                The request object.
     * @throws ImageNotFoundException
     */
    public function makeImage()
    {
        $request = $this->resolveRequestObject(func_get_args());

        if ($this->cacheFileExists($request) === true) {
            return $request;
        }

        if ($this->sourceFileExists($request) === false) {
            throw new ImageNotFoundException(
                'Could not find the image `'.$this->getSourceFilename($request).'`.'
            );
        }

        $source = $this->source->read(
            $this->getSourceFilename($request)
        );

        $this->cache->write(
            $this->getCacheFilename($request),
            $this->api->run($request, $source)
        );

        return $request;
    }
}
