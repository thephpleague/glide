<?php

namespace Glide;

use Glide\Exceptions\ImageNotFoundException;
use Glide\Interfaces\API as APIInterface;
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
     * @var APIInterface
     */
    private $api;

    /**
     * Signing key used to secure URLs.
     * @var string|null
     */
    private $signKey;

    /**
     * Create Server instance.
     * @param FilesystemInterface $source The source file system.
     * @param FilesystemInterface $cache  The cache file system.
     * @param APIInterface        $api    The image manipulation API.
     */
    public function __construct(FilesystemInterface $source, FilesystemInterface $cache, APIInterface $api)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setAPI($api);
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
     * @param APIInterface $cache The image manipulation API.
     */
    public function setAPI(APIInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Get the image manipulation API.
     * @return APIInterface The image manipulation API.
     */
    public function getAPI()
    {
        return $this->api;
    }

    /**
     * Set the signing key.
     * @param string $signKey Signing key used to secure URLs.
     */
    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * Get the signing key.
     * @return string Signing key used to secure URLs.
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
    public function getImageResponse($filename, Array $params)
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
        $request = new Request($filename, $params, $this->signKey);

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
