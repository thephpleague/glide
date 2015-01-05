<?php

namespace League\Glide\Middlewares;

use League\Glide\Output;
use League\Glide\Server;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ImageServer implements HttpKernelInterface
{
    /**
     * @var Server
     */
    private $server;

    /**
     * New Image Server Middleware
     * @param Server $server Configured instance of League\Glide\Server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $imageRequest = $this->server->makeImage($request->getPathInfo(), $request->query->all());

        $output = new Output($this->server->getCache());
        $response = $output->getResponse($imageRequest->getHash());

        return $response;
    }
}