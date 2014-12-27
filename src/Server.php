<?php

namespace Glide;

use Glide\Exceptions\ImageNotFoundException;
use League\Flysystem\Filesystem;

class Server
{
    private $source;
    private $cache;
    private $manipulator;
    private $signKey;

    public function __construct(Filesystem $source, Filesystem $cache, Manipulator $manipulator)
    {
        $this->setSource($source);
        $this->setCache($cache);
        $this->setManipulator($manipulator);
    }

    public function setSource(Filesystem $source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setCache(Filesystem $cache)
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function setManipulator(Manipulator $manipulator)
    {
        $this->manipulator = $manipulator;
    }

    public function getManipulator()
    {
        return $this->manipulator;
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

        $this->manipulator->setImage(
            $this->source->read(
                $request->getFilename()
            )
        );

        $this->manipulator->validate($request);

        if ($validateOnly) {
            return $request;
        }

        $this->cache->write(
            $request->getHash(),
            $this->manipulator->run($request)
        );

        return $request;
    }
}
