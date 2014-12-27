<?php

namespace Glide;

use League\Flysystem\Filesystem;

class Output
{
    private $cache;
    private $filename;

    public function __construct(Filesystem $cache, $filename)
    {
        $this->cache = $cache;
        $this->filename = $filename;
    }

    public function output()
    {
        $this->sendHeaders();
        $this->sendImage();
    }

    private function sendHeaders()
    {
        header_remove();
        header('Content-Type: ' . $this->cache->getMimetype($this->filename));
        header('Content-Length: ' . $this->cache->getSize($this->filename));
        header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT');
        header('Cache-Control: public, max-age=31536000');
        header('Pragma: public');
        flush();
    }

    private function sendImage()
    {
        $stream = $this->cache->readStream($this->filename);
        rewind($stream);
        fpassthru($stream);
        fclose($stream);
    }
}
