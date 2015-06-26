<?php

namespace League\Glide\Responses;

use League\Flysystem\FilesystemInterface;

interface ResponseFactoryInterface
{
    /**
     * Create the response.
     * @param  FilesystemInterface $cache The cache file system.
     * @param  string              $path  The cached file path.
     * @return mixed               The response object.
     */
    public function create(FilesystemInterface $cache, $path);

    /**
     * Send the response.
     * @param  FilesystemInterface $cache The cache file system.
     * @param  string              $path  The cached file path.
     * @return mixed               The response object.
     */
    public function send(FilesystemInterface $cache, $path);
}
