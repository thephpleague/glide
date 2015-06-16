<?php

namespace League\Glide;

use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Requests\RequestFactory;
use League\Glide\Responses\ResponseFactoryInterface;
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
     * @var FilesystemInterface
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
     * The response factory.
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * The base URL to exclude.
     * @var string
     */
    protected $baseUrl;

    /**
     * The default image manipulations.
     * @var array
     */
    protected $defaultManipulations = [];

    /**
     * Create Server instance.
     * @param FilesystemInterface      $source          The source file system.
     * @param FilesystemInterface      $cache           The cache file system.
     * @param ApiInterface             $api             The image manipulation API.
     * @param ResponseFactoryInterface $responseFactory The response factory.
     */
    public function __construct(FilesystemInterface $source, FilesystemInterface $cache, ApiInterface $api, ResponseFactoryInterface $responseFactory)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setApi($api);
        $this->setResponseFactory($responseFactory);
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
     * @return string                The source path.
     * @throws FileNotFoundException
     */
    public function getSourcePath()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        $path = trim($request->getPathInfo(), '/');

        if (substr($path, 0, strlen($this->baseUrl)) === $this->baseUrl) {
            $path = trim(substr($path, strlen($this->baseUrl)), '/');
        }

        if ($path === '') {
            throw new FileNotFoundException('Image path missing.');
        }

        if ($this->sourcePathPrefix) {
            $path = $this->sourcePathPrefix.'/'.$path;
        }

        return rawurldecode($path);
    }

    /**
     * Get the source path without the prefix.
     * @param  mixed
     * @return string The source path.
     */
    public function getSourcePathWithoutPrefix()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        $sourcePath = $this->getSourcePath($request);

        if ($this->sourcePathPrefix) {
            $sourcePath = substr($sourcePath, strlen($this->sourcePathPrefix) + 1);
        }

        return $sourcePath;
    }

    /**
     * Check if a source file exists.
     * @param  mixed
     * @return bool Whether the source file exists.
     */
    public function sourceFileExists()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        return $this->source->has($this->getSourcePath($request));
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
     * Set the cache path prefix.
     * @param string $cachePathPrefix The cache path prefix.
     */
    public function setCachePathPrefix($cachePathPrefix)
    {
        $this->cachePathPrefix = trim($cachePathPrefix, '/');
    }

    /**
     * Delete all cached manipulations for an image.
     * @param  string $path The source image path.
     * @return bool   Whether the delete succeeded.
     */
    public function deleteCache($path)
    {
        return $this->cache->deleteDir($path);
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
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        $sourcePath = $this->getSourcePathWithoutPrefix($request);

        $params = $request->query->all();
        unset($params['s']);
        ksort($params);

        $md5 = md5($sourcePath.'?'.http_build_query($params));

        $path = $sourcePath.'/'.$md5;

        if ($this->cachePathPrefix) {
            $path = $this->cachePathPrefix.'/'.$path;
        }

        return $path;
    }

    /**
     * Check if a cache file exists.
     * @param  mixed
     * @return bool Whether the cache file exists.
     */
    public function cacheFileExists()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

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
     * Set the response factory.
     * @param ResponseFactoryInterface $api The response factory.
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Get the response factory.
     * @return ResponseFactoryInterface The response factory.
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * Set the default image manipulations.
     * @param array $defaultManipulations The default image manipulations.
     */
    public function setDefaultManipulations($defaultManipulations = [])
    {
        $this->defaultManipulations = $defaultManipulations;
    }

    /**
     * Get the default image manipulations.
     * @return array The default image manipulations.
     */
    public function getDefaultManipulations()
    {
        return $this->defaultManipulations;
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
     * Generate and output image.
     * @param  mixed
     */
    public function outputImage()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        $path = $this->makeImage($request);

        $response = $this->responseFactory->getResponse($request, $this->cache, $path);

        $response->send();
    }

    /**
     * Generate and return image response object.
     * @param  mixed
     * @return StreamedResponse The response object.
     */
    public function getImageResponse()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        $path = $this->makeImage($request);

        return $this->responseFactory->getResponse($request, $this->cache, $path);
    }

    /**
     * Generate and return Base64 encoded image.
     * @param  mixed
     * @return string Base64 encoded image.
     */
    public function getImageAsBase64()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        $path = $this->makeImage($request);

        $source = $this->cache->read($path);

        return 'data:'.$this->cache->getMimetype($path).';base64,'.base64_encode($source);
    }

    /**
     * Generate manipulated image.
     * @return Request               The request object.
     * @throws FileNotFoundException
     */
    public function makeImage()
    {
        $request = RequestFactory::create(func_get_args(), $this->defaultManipulations);

        if ($this->cacheFileExists($request) === true) {
            return $this->getCachePath($request);
        }

        if ($this->sourceFileExists($request) === false) {
            throw new FileNotFoundException(
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

        // We need to write the image to the local disk before
        // doing any manipulations. This is because EXIF data
        // can only be read from an actual file.
        $tmp = tempnam(sys_get_temp_dir(), 'Glide');

        if (file_put_contents($tmp, $source) === false) {
            throw new FilesystemException(
                'Unable to write temp file for `'.$this->getSourcePath($request).'`.'
            );
        }

        try {
            $write = $this->cache->write(
                $this->getCachePath($request),
                $this->api->run($request, $tmp)
            );

            if ($write === false) {
                throw new FilesystemException(
                    'Could not write the image `'.$this->getCachePath($request).'`.'
                );
            }
        } catch (FileExistsException $exception) {
            // This edge case occurs when the target already exists
            // because it's currently be written to disk in another
            // request. It's best to just fail silently.
        }

        unlink($tmp);

        return $this->getCachePath($request);
    }
}
