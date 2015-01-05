<?php

namespace League\Glide\Factories;

use Symfony\Component\HttpFoundation\Request as ImageRequest;

class Request
{
    /**
     * Unique file identifier.
     * @var string
     */
    protected $filename;

    /**
     * Manipulation parameters.
     * @var array
     */
    protected $params;

    /**
     * Create request factory instance.
     * @param string $filename Unique file identifier.
     * @param array  $params   Manipulation parameters.
     */
    public function __construct($filename, array $params = [])
    {
        $this->filename = $filename;

        $this->params = [];
        foreach ($params as $key => $value) {
            $this->params[(string) $key] = (string) $value;
        }
    }

    /**
     * Create request instance.
     * @return ImageRequest The request object.
     */
    public function getRequest()
    {
        return new ImageRequest($this->params, [], [], [], [], [
            'REQUEST_URI' => $this->filename
        ]);
    }

    /**
     * Create request instance.
     * @param  string  $filename Unique file identifier.
     * @param  array   $params   Manipulation parameters.
     * @return Request The configured server.
     */
    public static function create($filename, array $params = [])
    {
        return (new self($filename, $params))->getRequest();
    }
}
