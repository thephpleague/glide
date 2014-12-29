<?php

namespace Glide;

use League\Flysystem\FilesystemInterface as Filesystem;

class Output
{
    private $cache;

    public function __construct(Filesystem $cache)
    {
        $this->setCache($cache);
    }

    public function setCache(Filesystem $cache)
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function output($filename)
    {
        $this->sendHeaders($filename);
        $this->sendImage($filename);
    }

    public function sendHeaders($filename)
    {
        $headers = [
            'Content-Type' => $this->cache->getMimetype($filename),
            'Content-Length' => $this->cache->getSize($filename),
            'Expires' => gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT',
            'Cache-Control' => 'public, max-age=31536000',
            'Pragma' => 'public',
        ];

        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }

        return $headers;
    }

    public function sendImage($filename)
    {
        $stream = $this->cache->readStream($filename);
        rewind($stream);
        fpassthru($stream);
        fclose($stream);
    }
}
