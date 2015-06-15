<?php

namespace League\Glide\Responses;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ResponseFactoryInterface
{
    /**
     * Get the response.
     * @param  Request             $request    The request object.
     * @param  FilesystemInterface $cache      The cache file system.
     * @param  string              $cachedPath The cached file path.
     * @return Response            The response object.
     */
    public function getResponse(Request $request, FilesystemInterface $cache, $cachedPath);
}
