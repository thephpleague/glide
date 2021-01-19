<?php

namespace League\Glide\Responses;

use Closure;
use League\Flysystem\FilesystemException as V2FilesystemException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FilesystemOperator;
use League\Glide\Filesystem\FilesystemException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class PsrResponseFactory implements ResponseFactoryInterface, Flysystem2ResponseFactoryInterface
{
    /**
     * Base response object.
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Callback to create stream.
     * @var Closure
     */
    protected $streamCallback;

    /**
     * Create PsrResponseFactory instance.
     * @param ResponseInterface $response       Base response object.
     * @param Closure           $streamCallback Callback to create stream.
     */
    public function __construct(ResponseInterface $response, Closure $streamCallback)
    {
        $this->response = $response;
        $this->streamCallback = $streamCallback;
    }

    /**
     * Build response object
     * @param StreamInterface $stream
     * @param string          $contentType
     * @param string          $contentLength
     * @return ResponseInterface Response object.
     * @throws FilesystemException
     */
    private function createResponse($stream, $contentType, $contentLength)
    {
        $cacheControl = 'max-age=31536000, public';
        $expires = date_create('+1 years')->format('D, d M Y H:i:s').' GMT';

        if ($contentType === false) {
            throw new FilesystemException('Unable to determine the image content type.');
        }

        if ($contentLength === false) {
            throw new FilesystemException('Unable to determine the image content length.');
        }

        return $this->response->withBody($stream)
                              ->withHeader('Content-Type', $contentType)
                              ->withHeader('Content-Length', $contentLength)
                              ->withHeader('Cache-Control', $cacheControl)
                              ->withHeader('Expires', $expires);
    }

    /**
     * Create response.
     * @param  FilesystemInterface $cache Cache file system.
     * @param  string              $path  Cached file path.
     * @return ResponseInterface   Response object.
     * @throws FilesystemException
     */
    public function create(FilesystemInterface $cache, $path)
    {
        $stream = $this->streamCallback->__invoke(
            $cache->readStream($path)
        );

        $contentType = $cache->getMimetype($path);
        $contentLength = (string) $cache->getSize($path);

        return $this->createResponse($stream, $contentType, $contentLength);
    }

    /**
     * Create response.
     * @param  FilesystemOperator  $cache  Cache file system.
     * @param  string  $path  Cached file path.
     * @return ResponseInterface   Response object.
     * @throws FilesystemException
     */
    public function createFlysystem2(FilesystemOperator $cache, $path)
    {
        try {
            $stream = $this->streamCallback->__invoke(
                $cache->readStream($path)
            );

            $contentType = $cache->mimeType($path);
            $contentLength = (string) $cache->fileSize($path);

            return $this->createResponse($stream, $contentType, $contentLength);
        } catch (V2FilesystemException $exception) {
            throw new FilesystemException($exception->getMessage());
        }
    }
}
