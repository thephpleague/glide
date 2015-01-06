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
     * Secret key used to secure URLs.
     * @var SignKey
     */
    protected $signKey;

    /**
     * Create Server instance.
     * @param FilesystemInterface $source  The source file system.
     * @param FilesystemInterface $cache   The cache file system.
     * @param ApiInterface        $api     The image manipulation API.
     * @param SignKey             $signKey Secret key used to secure URLs.
     */
    public function __construct(FilesystemInterface $source, FilesystemInterface $cache, ApiInterface $api, SignKey $signKey = null)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setApi($api);
        $this->setSignKey($signKey);
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
     * Set the sign key.
     * @param SignKey $signKey Secret key used to secure URLs.
     */
    public function setSignKey(SignKey $signKey = null)
    {
        $this->signKey = $signKey;
    }

    /**
     * Get the sign key.
     * @return SignKey Secret key used to secure URLs.
     */
    public function getSignKey()
    {
        return $this->signKey;
    }

    /**
     * Get a cache filename.
     * @param  Request $request The request object.
     * @return string  The cache filename.
     */
    public function getCacheFilename(Request $request)
    {
        $params = $request->query->all();

        unset($params['token']);

        return md5($request->getPathInfo().'?'.http_build_query($params));
    }

    /**
     * Check if the cache file exists
     * @param  Request $request
     * @return bool
     */
    public function cacheFileExists(Request $request)
    {
        return $this->cache->has($this->getCacheFilename($request));
    }

    /**
     * Check if the source file exists
     * @param  Request $request
     * @return bool
     */
    public function sourceFileExists(Request $request)
    {
        return $this->source->has($request->getPathInfo());
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
     * Generate and output manipulated image.
     * @param  mixed
     * @return Request The request object.
     */
    public function outputImage()
    {
        $request = call_user_func_array([$this, 'makeImage'], func_get_args());

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
        $request = call_user_func_array([$this, 'makeImage'], func_get_args());

        $output = new Output($this->cache);

        return $output->getResponse($this->getCacheFilename($request));
    }

    /**
     * Generate manipulated image.
     * @return Request                          The request object.
     * @throws Exceptions\InvalidTokenException
     * @throws ImageNotFoundException
     */
    public function makeImage()
    {
        $request = $this->resolveRequestObject(func_get_args());

        if ($this->signKey) {
            $this->signKey->validateRequest($request);
        }

        if ($this->cacheFileExists($request) === true) {
            return $request;
        }

        if ($this->sourceFileExists($request) === false) {
            throw new ImageNotFoundException(
                'Could not find the image `'.$request->getPathInfo().'`.'
            );
        }

        $source = $this->source->read(
            $request->getPathInfo()
        );

        $this->cache->write(
            $this->getCacheFilename($request),
            $this->api->run($request, $source)
        );

        return $request;
    }
}
