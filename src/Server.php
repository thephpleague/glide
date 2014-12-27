<?php

namespace Glide;

use Glide\Exceptions\ImageNotFoundException;
use Glide\Interfaces\API as APIInterface;
use League\Flysystem\FilesystemInterface;

class Server
{
    private $source;
    private $cache;
    private $api;
    private $signKey;

    public function __construct(FilesystemInterface $source, FilesystemInterface $cache, APIInterface $api)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setAPI($api);
    }

    public function setSource(FilesystemInterface $source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setCache(FilesystemInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function setAPI(APIInterface $api)
    {
        $this->api = $api;
    }

    public function getAPI()
    {
        return $this->api;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function getSignKey()
    {
        return $this->signKey;
    }

    public function test($filename, Array $params = [])
    {
        return $this->make($filename, $params, true);
    }

    public function output($filename, Array $params = [])
    {
        $request = $this->make($filename, $params);

        $output = new Output($this->cache, $request->getHash());
        $output->output();

        return $request;
    }

    public function make($filename, Array $params = [], $validateOnly = false)
    {
        $request = new Request($filename, $params, $this->signKey);

        if ($this->cache->has($request->getHash())) {
            return $request;
        }

        if (!$this->source->has($request->getFilename())) {
            throw new ImageNotFoundException(
                'Could not find the file: ' . $request->getFilename()
            );
        }

        $source = $this->source->read(
            $request->getFilename()
        );

        $this->api->validate($request, $source);

        if ($validateOnly) {
            return $request;
        }

        $this->cache->write(
            $request->getHash(),
            $this->api->run($request, $source)
        );

        return $request;
    }
}
