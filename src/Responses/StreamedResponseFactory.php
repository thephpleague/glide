<?php

namespace League\Glide\Responses;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamedResponseFactory implements ResponseFactoryInterface
{
    /**
     * Get the streamed response.
     * @param  Request             $request The request object.
     * @param  FilesystemInterface $cache   The cache file system.
     * @param  string              $path    The cached file cachedPath.
     * @return StreamedResponse    The response object.
     */
    public function getResponse(Request $request, FilesystemInterface $cache, $cachedPath)
    {
        $stream = $cache->readStream($cachedPath);

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $cache->getMimetype($cachedPath));
        $response->headers->set('Content-Length', $cache->getSize($cachedPath));
        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setExpires(date_create()->modify('+1 years'));
        $response->setLastModified(date_create()->setTimestamp($cache->getTimestamp($cachedPath)));
        $response->isNotModified($request);
        $response->setCallback(function () use ($stream) {
            rewind($stream);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}
