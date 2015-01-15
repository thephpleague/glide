<?php

namespace League\Glide;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Output
{
    /**
     * The cache file system.
     * @var FilesystemInterface
     */
    protected $cache;

    /**
     * Create Output instance.
     * @param FilesystemInterface $cache The cache file system.
     */
    public function __construct(FilesystemInterface $cache)
    {
        $this->setCache($cache);
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
     * Get the streamed response.
     * @param  string           $path The cache path.
     * @return StreamedResponse The response object.
     */
    public function getResponse($path)
    {
        $response = new StreamedResponse();

        $this->setHeaders($response, $path);
        $this->setContent($response, $path);

        return $response;
    }

    /**
     * Set the streamed response headers.
     * @param  StreamedResponse $response The response object.
     * @param  string           $path     The cache path.
     * @return StreamedResponse
     */
    public function setHeaders(StreamedResponse $response, $path)
    {
        $response->headers->set('Content-Type', $this->cache->getMimetype($path));
        $response->headers->set('Content-Length', $this->cache->getSize($path));
        $response->setPublic();
        $response->setExpires(date_create('now')->modify('+1 years'));
        $response->setMaxAge(31536000);

        return $response;
    }

    /**
     * Set the stream response content.
     * @param  StreamedResponse $response The response object.
     * @param  string           $path     The cache path.
     * @return StreamedResponse
     */
    public function setContent(StreamedResponse $response, $path)
    {
        $stream = $this->cache->readStream($path);

        $response->setCallback(function () use ($stream) {
            rewind($stream);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
