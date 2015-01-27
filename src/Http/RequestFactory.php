<?php

namespace League\Glide\Http;

use Symfony\Component\HttpFoundation\Request;

class RequestFactory
{
    /**
     * The source path.
     * @var string
     */
    protected $path;

    /**
     * Manipulation parameters.
     * @var array
     */
    protected $params;

    /**
     * Create request factory instance.
     * @param string $path   The source path.
     * @param array  $params Manipulation parameters.
     */
    public function __construct($path, array $params = [])
    {
        $this->path = $path;

        $this->params = [];
        foreach ($params as $key => $value) {
            $this->params[(string) $key] = (string) $value;
        }
    }

    /**
     * Create request instance.
     * @return Request The request object.
     */
    public function getRequest()
    {
        return new Request(
            $this->params,
            [],
            [],
            [],
            [],
            array_merge($_SERVER, ['REQUEST_URI' => $this->path])
        );
    }

    /**
     * Create request instance.
     * @param  string  $path   The source path.
     * @param  array   $params Manipulation parameters.
     * @return Request The request object.
     */
    public static function create($path, array $params = [])
    {
        return (new self($path, $params))->getRequest();
    }
}
