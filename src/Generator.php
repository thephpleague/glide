<?php

namespace Glide;

use Glide\API\APIInterface;
use Glide\Exceptions\ImageNotFoundException;

class Generator
{
    private $source;
    private $cache;
    private $api;

    public function __construct(Storage $source, Storage $cache, APIInterface $api)
    {
        $this->source = $source;
        $this->cache = $cache;
        $this->api = $api;
    }

    public function generate(Request $request)
    {
        if ($this->cache->has($request->getHash())) {
            return;
        }

        if (!$this->source->has($request->getFilename())) {
            throw new ImageNotFoundException('Could not find the file: ' . $request->getFilename());
        }

        $this->cache->write(
            $request->getHash(),
            $this->api->run(
                $request,
                $this->source->read(
                    $request->getFilename()
                )
            )
        );
    }
}
