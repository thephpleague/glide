<?php

namespace League\Glide\Responses;

use Closure;
use Slim\Http\Response;
use Slim\Http\Stream;

class SlimResponseFactory extends PsrResponseFactory
{
    /**
     * Base response object.
     * @var Response
     */
    protected $response;

    /**
     * Callback to create stream.
     * @var Closure
     */
    protected $streamCallback;

    /**
     * Create SlimResponseFactory instance.
     */
    public function __construct()
    {
        $this->response = new Response();
        $this->streamCallback = function ($stream) {
            return new Stream($stream);
        };
    }
}
