<?php

namespace Glide;

use Glide\API\APIInterface;
use Intervention\Image\ImageManager;

class Server
{
    private $source;
    private $cache;
    private $api;
    private $signKey;

    public function __construct($source, $cache, APIInterface $api)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setAPI($api);
    }

    public function setSource($source)
    {
        $this->source = new Storage($source);
    }

    public function getSource()
    {
        return $this->source->get();
    }

    public function setCache($cache)
    {
        $this->cache = new Storage($cache);
    }

    public function getCache()
    {
        return $this->cache->get();
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

    public function output($filename, Array $params = [])
    {
        $request = $this->processRequest($filename, $params);

        $output = new Output($this->cache, $request);
        $output->output();

        return $request;
    }

    public function generate($filename, Array $params = [])
    {
        $request = $this->processRequest($filename, $params);

        return $request;
    }

    private function processRequest($filename, Array $params = [])
    {
        $request = new Request($filename, $params, $this->signKey);

        $generator = new Generator(
            $this->source,
            $this->cache,
            $this->api
        );
        $generator->generate($request);

        return $request;
    }
}
