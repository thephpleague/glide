<?php
declare(strict_types=1);

namespace League\Glide\Responses;

use League\Flysystem\FilesystemOperator;

interface Flysystem2ResponseFactoryInterface
{
    /**
     * Create response.
     * @param  FilesystemOperator  $cache Cache file system.
     * @param  string              $path  Cached file path.
     * @return mixed               The response object.
     */
    public function createFlysystem2(FilesystemOperator $cache, $path);
}
