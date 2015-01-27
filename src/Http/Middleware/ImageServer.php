<?php

namespace League\Glide\Http\Middleware;

use League\Glide\Server;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ImageServer implements HttpKernelInterface
{
    /**
     * Configured instance of server.
     * @var Server
     */
    protected $server;

    /**
     * Instance of app.
     * @var HttpKernelInterface
     */
    protected $app;

    /**
     * Create ImageServer instance.
     * @param HttpKernelInterface $app
     * @param Server              $server Configured instance of server.
     */
    public function __construct(HttpKernelInterface $app, Server $server)
    {
        $this->app = $app;
        $this->server = $server;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return $this->server->getImageResponse($request);
    }
}
