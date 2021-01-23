<?php

namespace League\Glide\Responses;

use Closure;
use League\Flysystem\FilesystemInterface;
use League\Glide\Filesystem\FilesystemException;
use Psr\Http\Message\ResponseInterface;

class PsrResponseFactory implements ResponseFactoryInterface
{
    /**
     * Base response object.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Callback to create stream.
     *
     * @var Closure
     */
    protected $streamCallback;

    /**
     * Create PsrResponseFactory instance.
     *
     * @param ResponseInterface $response       Base response object.
     * @param Closure           $streamCallback Callback to create stream.
     */
    public function __construct(ResponseInterface $response, Closure $streamCallback)
    {
        $this->response = $response;
        $this->streamCallback = $streamCallback;
    }

    /**
     * Create response.
     *
     * @param FilesystemInterface $cache Cache file system.
     * @param string              $path  Cached file path.
     *
     * @return ResponseInterface Response object.
     */
    public function create(FilesystemInterface $cache, $path)
    {
        $stream = $this->streamCallback->__invoke(
            $cache->readStream($path)
        );

        $contentType = $cache->getMimetype($path);
        $contentLength = (string) $cache->getSize($path);
        $cacheControl = 'max-age=31536000, public';
        $expires = date_create('+1 years')->format('D, d M Y H:i:s').' GMT';

        if (false === $contentType) {
            throw new FilesystemException('Unable to determine the image content type.');
        }

        if (false === $contentLength) {
            throw new FilesystemException('Unable to determine the image content length.');
        }

        return $this->response->withBody($stream)
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Content-Length', $contentLength)
            ->withHeader('Cache-Control', $cacheControl)
            ->withHeader('Expires', $expires);
    }
}
