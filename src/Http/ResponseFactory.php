<?php

namespace League\Glide\Http;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseFactory
{
    /**
     * The cache file system.
     * @var FilesystemInterface
     */
    protected $cache;

    /**
     * The request object.
     * @var Request
     */
    protected $request;

    /**
     * The file path.
     * @var string
     */
    protected $path;

    /**
     * Create Output instance.
     * @param FilesystemInterface $cache   The cache file system.
     * @param Request             $request The request object.
     * @param string              $path    The file path.
     */
    public function __construct(FilesystemInterface $cache, Request $request, $path)
    {
        $this->cache = $cache;
        $this->request = $request;
        $this->path = $path;
    }

    /**
     * Get the streamed response.
     * @return StreamedResponse The response object.
     */
    public function getResponse()
    {
        $response = new StreamedResponse();

        $this->setHeaders($response);
        $this->setContent($response);

        return $response;
    }

    /**
     * Set the streamed response headers.
     * @param  StreamedResponse $response The response object.
     * @return StreamedResponse
     */
    public function setHeaders(StreamedResponse $response)
    {
        $response->headers->set('Content-Type', $this->cache->getMimetype($this->path));
        $response->headers->set('Content-Length', $this->cache->getSize($this->path));

        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setExpires(date_create()->modify('+1 years'));
        $response->setLastModified(date_create()->setTimestamp($this->cache->getTimestamp($this->path)));
        $response->isNotModified($this->request);

        return $response;
    }

    /**
     * Set the stream response content.
     * @param  StreamedResponse $response The response object.
     * @return StreamedResponse
     */
    public function setContent(StreamedResponse $response)
    {
        $stream = $this->cache->readStream($this->path);

        $response->setCallback(function () use ($stream) {
            rewind($stream);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * Create response instance.
     * @param  FilesystemInterface $cache   The cache file system.
     * @param  Request             $request The request object.
     * @param  string              $path    The file path.
     * @return StreamedResponse    The response object.
     */
    public static function create(FilesystemInterface $cache, Request $request, $path)
    {
        return (new self($cache, $request, $path))->getResponse();
    }
}
