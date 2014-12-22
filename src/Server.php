<?php

namespace Glide;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Intervention\Image\ImageManager;

class Server
{
    private $source;
    private $cache;
    private $manager;
    private $signKey;

    public function __construct($source, $cache = null, $manager = null)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setManager($manager);
    }

    public function setSource($source)
    {
        if (is_string($source)) {
            $this->source = new Filesystem(new Local($source));
        } else if ($source instanceof Filesystem) {
            $this->source = $source;
        } else {
            throw new \Exception('Not a valid source.');
        }
    }

    public function setCache($cache)
    {
        if (is_string($cache)) {
            $this->cache = new Filesystem(new Local($cache));
        } else if ($cache instanceof Filesystem) {
            $this->cache = $cache;
        } else {
            throw new \Exception('Not a valid cache.');
        }
    }

    public function setManager($manager)
    {
        if (is_null($manager)) {
            $this->manager = new ImageManager();
        } elseif ($manager instanceof ImageManager) {
            $this->manager = $manager;
        } else {
            throw new \Exception('Invalid manager, must be an instance of ImageManager.');
        }
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function output($filename, $params)
    {
        $url = new Url($filename, $params);

        if (!$this->cache->has($url->getHash())) {
            $this->generateImage($url);
        }

        $this->outputImage($url);
    }

    private function generateImage(Url $url)
    {
        if (!$this->source->has($url->getFilename())) {
            throw new ImageNotFoundException('Could not find the file: ' . $url->getFilename());
        }

        $api = new API($url->getParams());

        $this->cache->write(
            $url->getHash(),
            $api->run(
                $this->manager->make(
                    $this->source->read(
                        $url->getFilename()
                    )
                )
            )
        );
    }

    private function outputImage(Url $url)
    {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        header_remove();
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . $this->cache->getSize($url->getHash()));
        header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT');
        header('Cache-Control: public, max-age=31536000');
        header('Pragma: public');
        flush();

        $stream = $this->cache->readStream($url->getHash());
        rewind($stream);
        fpassthru($stream);
        fclose($stream);

        exit;
    }
}
