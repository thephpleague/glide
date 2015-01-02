<?php

namespace Glide;

use Glide\Exceptions\ImageNotFoundException;
use Glide\Interfaces\Api as ApiInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Server
{
    /**
     * The source file system.
     * @var FilesystemInterface
     */
    private $source;

    /**
     * The cache file system.
     * @var FilesystemInterface
     */
    private $cache;

    /**
     * The image manipulation API.
     * @var ApiInterface
     */
    private $api;

    /**
     * Secret key used to secure URLs.
     * @var SignKey
     */
    private $signKey;

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
     * Generate and output manipulated image.
     * @param  string  $filename Unique file identifier.
     * @param  Array   $params   Manipulation parameters.
     * @return Request The request object.
     */
    public function outputImage($filename, Array $params = [])
    {
        $request = $this->makeImage($filename, $params);

        $output = new Output($this->cache);
        $output->getResponse($request->getHash())->send();

        return $request;
    }

    /**
     * Generate and return response object of manipulated image.
     * @param  string           $filename Unique file identifier.
     * @param  Array            $params   Manipulation parameters.
     * @return StreamedResponse The response object.
     */
    public function getImageResponse($filename, Array $params = [])
    {
        $request = $this->makeImage($filename, $params);

        $output = new Output($this->cache);

        return $output->getResponse($request->getHash());
    }

    /**
     * Generate manipulated image.
     * @param  string  $filename Unique file identifier.
     * @param  Array   $params   Manipulation parameters.
     * @return Request The request object.
     */
    public function makeImage($filename, Array $params = [])
    {
        $request = new Request($filename, $params);

        if ($this->signKey) {
            $this->signKey->validateRequest($request);
        }

        if ($this->cache->has($request->getHash())) {
            return $request;
        }

        if (!$this->source->has($request->getFilename())) {
            throw new ImageNotFoundException(
                'Could not find the image `'.$request->getFilename().'`.'
            );
        }

        $source = $this->source->read(
            $request->getFilename()
        );

        $this->cache->write(
            $request->getHash(),
            $this->api->run($request, $source)
        );

        return $request;
    }
}
