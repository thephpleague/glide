<?php

namespace Glide;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Output
{
    private $cache;

    public function __construct(FilesystemInterface $cache)
    {
        $this->setCache($cache);
    }

    public function setCache(FilesystemInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getResponse($filename)
    {
        $response = new StreamedResponse();

        $this->setHeaders($response, $filename);
        $this->setContent($response, $filename);

        return $response;
    }

    public function setHeaders(StreamedResponse $response, $filename)
    {
        $response->headers->set('Content-Type', $this->cache->getMimetype($filename));
        $response->headers->set('Content-Length', $this->cache->getSize($filename));
        $response->setPublic();
        $response->setExpires(date_create('now')->modify('+1 years'));
        $response->setMaxAge(31536000);

        return $response;
    }

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
