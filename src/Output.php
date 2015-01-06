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
     * @param  string           $filename Unique file identifier.
     * @return StreamedResponse The response object.
     */
    public function getResponse($filename)
    {
        $response = new StreamedResponse();

        $this->setHeaders($response, $filename);
        $this->setContent($response, $filename);

        return $response;
    }

    /**
     * Set the streamed response headers.
     * @param  StreamedResponse $response The response object.
     * @param  string           $filename Unique file identifier.
     * @return StreamedResponse
     */
    public function setHeaders(StreamedResponse $response, $filename)
    {
        $response->headers->set('Content-Type', $this->cache->getMimetype($filename));
        $response->headers->set('Content-Length', $this->cache->getSize($filename));
        $response->setPublic();
        $response->setExpires(date_create('now')->modify('+1 years'));
        $response->setMaxAge(31536000);

        return $response;
    }

    /**
     * Set the stream response content.
     * @param  StreamedResponse $response The response object.
     * @param  string           $filename Unique file identifier.
     * @return StreamedResponse
     */
    public function setContent(StreamedResponse $response, $filename)
    {
        $stream = $this->cache->readStream($filename);

        $response->setCallback(function () use ($stream) {
            rewind($stream);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
