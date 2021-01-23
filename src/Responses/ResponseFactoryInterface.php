<?php

namespace League\Glide\Responses;

use League\Flysystem\FilesystemInterface;

interface ResponseFactoryInterface
{
    /**
     * Create response.
     *
     * @param FilesystemInterface $cache Cache file system.
     * @param string              $path  Cached file path.
     *
     * @return mixed The response object.
     */
    public function create(FilesystemInterface $cache, $path);
}
