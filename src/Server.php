<?php

namespace Glide;

use Intervention\Image\ImageManager;

class Server
{
    private $source;
    private $cache;
    private $driver;
    private $signKey;

    public function __construct($source, $cache = null, $driver = 'gd')
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setDriver($driver);
    }

    public function setSource($source)
    {
        $this->source = new Storage($source);
    }

    public function setCache($cache)
    {
        $this->cache = new Storage($cache);
    }

    public function setDriver($driver)
    {
        if (!in_array($driver, ['gd', 'imagick'])) {
            throw new ConfigurationException('Not a valid driver, accepts "gd" or "imagick".');
        }

        $this->driver = $driver;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function getSignKey()
    {
        return $this->signKey;
    }

    public function output($filename, $params)
    {
        return $this->outputImage(
            $this->generate($filename, $params)
        );
    }

    public function generate($filename, $params)
    {
        $request = new Request($filename, $params, $this->signKey);

        if (!$this->cache->has($request->getHash())) {
            $this->generateImage($request);
        }

        return $request;
    }

    private function generateImage(Request $request)
    {
        if (!$this->source->has($request->getFilename())) {
            throw new ImageNotFoundException('Could not find the file: ' . $request->getFilename());
        }

        $api = new API($request->getParams());
        $manager = new ImageManager(['driver' => $this->driver]);

        $this->cache->write(
            $request->getHash(),
            $api->run(
                $manager->make(
                    $this->source->read(
                        $request->getFilename()
                    )
                )
            )
        );

        return $request;
    }

    private function outputImage(Request $request)
    {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        header_remove();
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . $this->cache->getSize($request->getHash()));
        header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT');
        header('Cache-Control: public, max-age=31536000');
        header('Pragma: public');
        flush();

        $this->cache->readStream($request->getHash());

        return $request;
    }
}
