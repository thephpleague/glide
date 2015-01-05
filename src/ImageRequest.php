<?php

namespace League\Glide;

class ImageRequest
{
    /**
     * Unique file identifier.
     * @var string
     */
    private $filename;

    /**
     * Manipulation parameters.
     * @var Array
     */
    private $params;

    /**
     * Create ImageRequest instance.
     * @param string $filename Unique file identifier.
     * @param Array  $params   Manipulation parameters.
     */
    public function __construct($filename, Array $params = [])
    {
        $this->setFilename($filename);
        $this->setParams($params);
    }

    /**
     * Set the filename.
     * @param string $filename Unique file identifier.
     */
    public function setFilename($filename)
    {
        $this->filename = ltrim($filename, '/');
    }

    /**
     * Get the filename.
     * @return string Unique file identifier.
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the params.
     * @param Array $params Manipulation parameters.
     */
    public function setParams(Array $params)
    {
        ksort($params);

        $this->params = $params;
    }

    /**
     * Get the params.
     * @return Array Manipulation parameters.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get a specific param.
     * @param  string $key Parameter name.
     * @return string Manipulation parameter.
     */
    public function getParam($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
    }

    /**
     * Get the request hash.
     * @return string Generated hash.
     */
    public function getHash()
    {
        $params = $this->params;

        unset($params['token']);

        return md5($this->filename.'?'.http_build_query($params));
    }
}
