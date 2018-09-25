<?php


namespace League\Glide\Responses;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use League\Glide\Filesystem\FilesystemException;
use Psr\Http\Message\ResponseFactoryInterface as PsrResponseFactory;
use Psr\Http\Message\ResponseInterface;

class Psr17ResponseFactory implements ResponseFactoryInterface
{

    protected $responseFactory;

    public function __construct(PsrResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Create response.
     * @param  FilesystemInterface $cache Cache file system.
     * @param  string              $path Cached file path.
     * @return ResponseInterface   The response object.
     * @throws FileNotFoundException;
     * @throws FilesystemException;
     */
    public function create(FilesystemInterface $cache, $path)
    {
        $body = $cache->readStream($path);
        $contentType = $cache->getMimetype($path);
        $contentLength = (string) $cache->getSize($path);
        $cacheControl = 'max-age=31536000, public';
        $expires = date_create('+1 years')->format('D, d M Y H:i:s') . ' GMT';

        if ($contentType === false) {
            throw new FilesystemException('Unable to determine the image content type.');
        }

        if ($contentLength === false) {
            throw new FilesystemException('Unable to determine the image content length.');
        }

        $response = $this->responseFactory->createResponse(200, 'OK');
        $response->getBody()->write($body);

        return $response
            ->withHeader('Content-Type', $contentType)
            ->withHeader('Content-Length', $contentLength)
            ->withHeader('Cache-Control', $cacheControl)
            ->withHeader('Expires', $expires);
    }
}