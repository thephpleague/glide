<?php

namespace League\Glide;

use InvalidArgumentException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Responses\ResponseFactoryInterface;

class Server
{
    /**
     * Source file system.
     * @var FilesystemInterface
     */
    protected $source;

    /**
     * Source path prefix.
     * @var string
     */
    protected $sourcePathPrefix;

    /**
     * Cache file system.
     * @var FilesystemInterface
     */
    protected $cache;

    /**
     * Cache path prefix.
     * @var string
     */
    protected $cachePathPrefix;

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
    protected $baseUrl;

    /**
     * Default image manipulations.
     * @var array
     */
    protected $defaultManipulations = [];

    /**
     * Create Server instance.
     * @param FilesystemInterface      $source          Source file system.
     * @param FilesystemInterface      $cache           Cache file system.
     * @param ApiInterface             $api             Image manipulation API.
     * @param ResponseFactoryInterface $responseFactory Response factory.
     */
    public function __construct(FilesystemInterface $source, FilesystemInterface $cache, ApiInterface $api, ResponseFactoryInterface $responseFactory = null)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setApi($api);
        $this->setResponseFactory($responseFactory);
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

    /**
     * Set source path prefix.
     * @param string $sourcePathPrefix Source path prefix.
     */
    public function setSourcePathPrefix($sourcePathPrefix)
    {
        $this->sourcePathPrefix = trim($sourcePathPrefix, '/');
    }

    /**
     * Get source path prefix.
     * @return string Source path prefix.
     */
    public function getSourcePathPrefix()
    {
        return $this->sourcePathPrefix;
    }

    /**
     * Get source path.
     * @param  string                $path Image path.
     * @return string                The source path.
     * @throws FileNotFoundException
     */
    public function getSourcePath($path)
    {
        $path = trim($path, '/');

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
     * Check if a source file exists.
     * @param  string $path Image path.
     * @return bool   Whether the source file exists.
     */
    public function sourceFileExists($path)
    {
        return $this->source->has($this->getSourcePath($path));
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
     * Set cache path prefix.
     * @param string $cachePathPrefix Cache path prefix.
     */
    public function setCachePathPrefix($cachePathPrefix)
    {
        $this->cachePathPrefix = trim($cachePathPrefix, '/');
    }

    /**
     * Get cache path prefix.
     * @return string Cache path prefix.
     */
    public function getCachePathPrefix()
    {
        return $this->cachePathPrefix;
    }

    /**
     * Get cache path.
     * @param  string $path   Image path.
     * @param  array  $params Image manipulation params.
     * @return string Cache path.
     */
    public function getCachePath($path, array $params)
    {
        $sourcePath = $this->getSourcePath($path);

        if ($this->sourcePathPrefix) {
            $sourcePath = substr($sourcePath, strlen($this->sourcePathPrefix) + 1);
        }

        $params = array_merge($this->defaultManipulations, $params);
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
     * @param  string $path   Image path.
     * @param  array  $params Image manipulation params.
     * @return bool   Whether the cache file exists.
     */
    public function cacheFileExists($path, array $params)
    {
        return $this->cache->has(
            $this->getCachePath($path, $params)
        );
    }

    /**
     * Delete cached manipulations for an image.
     * @param  string $path Image path.
     * @return bool   Whether the delete succeeded.
     */
    public function deleteCache($path)
    {
        return $this->cache->deleteDir($path);
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
     * @param array $defaultManipulations Default image manipulations.
     */
    public function setDefaultManipulations(array $defaultManipulations)
    {
        $this->defaultManipulations = $defaultManipulations;
    }

    /**
     * Get default image manipulations.
     * @return array Default image manipulations.
     */
    public function getDefaultManipulations()
    {
        return $this->defaultManipulations;
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
     * Set response factory.
     * @param ResponseFactoryInterface|null $responseFactory Response factory.
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory = null)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Get response factory.
     * @return ResponseFactoryInterface Response factory.
     */
    public function getResponseFactory()
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
    public function getImageResponse($path, array $params)
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
    public function getImageAsBase64($path, array $params)
    {
        $path = $this->makeImage($path, $params);

        $source = $this->cache->read($path);

        if ($source === false) {
            throw new FilesystemException(
                'Could not read the image `'.$path.'`.'
            );
        }

        return 'data:'.$this->cache->getMimetype($path).';base64,'.base64_encode($source);
    }

    /**
     * Generate and output image.
     * @param  string                   $path   Image path.
     * @param  array                    $params Image manipulation params.
     * @throws InvalidArgumentException
     */
    public function outputImage($path, array $params)
    {
        if (is_null($this->responseFactory)) {
            throw new InvalidArgumentException(
                'Unable to output image, no response factory defined.'
            );
        }

        $path = $this->makeImage($path, $params);

        $this->responseFactory->send($this->cache, $path);
    }

    /**
     * Generate manipulated image.
     * @param  string                $path   Image path.
     * @param  array                 $params Image manipulation params.
     * @return string                Cache path.
     * @throws FileNotFoundException
     * @throws FilesystemException
     */
    public function makeImage($path, array $params)
    {
        $sourcePath = $this->getSourcePath($path);
        $cachedPath = $this->getCachePath($path, $params);

        if ($this->cacheFileExists($path, $params) === true) {
            return $cachedPath;
        }

        if ($this->sourceFileExists($path) === false) {
            throw new FileNotFoundException(
                'Could not find the image `'.$sourcePath.'`.'
            );
        }

        $source = $this->source->read(
            $sourcePath
        );

        if ($source === false) {
            throw new FilesystemException(
                'Could not read the image `'.$sourcePath.'`.'
            );
        }

        // We need to write the image to the local disk before
        // doing any manipulations. This is because EXIF data
        // can only be read from an actual file.
        $tmp = tempnam(sys_get_temp_dir(), 'Glide');

        if (file_put_contents($tmp, $source) === false) {
            throw new FilesystemException(
                'Unable to write temp file for `'.$sourcePath.'`.'
            );
        }

        try {
            $write = $this->cache->write(
                $cachedPath,
                $this->api->run($tmp, array_merge($this->defaultManipulations, $params))
            );

            if ($write === false) {
                throw new FilesystemException(
                    'Could not write the image `'.$cachedPath.'`.'
                );
            }
        } catch (FileExistsException $exception) {
            // This edge case occurs when the target already exists
            // because it's currently be written to disk in another
            // request. It's best to just fail silently.
        }

        unlink($tmp);

        return $cachedPath;
    }
}
