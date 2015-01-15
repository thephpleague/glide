<?php

namespace League\Glide\Factories;

use Symfony\Component\HttpFoundation\Request as ImageRequest;

class Request
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
     * @return ImageRequest The request object.
     */
    public function getRequest()
    {
        return new ImageRequest($this->params, [], [], [], [], [
            'REQUEST_URI' => $this->path
        ]);
    }

    /**
     * Create request instance.
     * @param  string       $path   The source path.
     * @param  array        $params Manipulation parameters.
     * @return ImageRequest The request object.
     */
    public static function create($path, array $params = [])
    {
        return (new self($path, $params))->getRequest();
    }
}
