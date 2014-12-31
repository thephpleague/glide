<?php

namespace Glide;

class Token
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
     * Signing key used to secure URLs.
     * @var string|null
     */
    private $signKey;

    /**
     * Create Token instance.
     * @param strin  $filename Unique file identifier.
     * @param Array  $params   Manipulation parameters.
     * @param string $signKey  Signing key used to secure URLs.
     */
    public function __construct($filename, Array $params, $signKey)
    {
        $this->setFilename($filename);
        $this->setParams($params);
        $this->setSignKey($signKey);
    }

    /**
     * Set the filename.
     * @param string $filename Unique file identifier.
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
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
     * Set the signing key.
     * @param string $signKey Signing key used to secure URLs.
     */
    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * Get the signing key.
     * @return string Signing key used to secure URLs.
     */
    public function getSignKey()
    {
        return $this->signKey;
    }

    /**
     * Get a secure token.
     * @return string Generated secure token.
     */
    public function generate()
    {
        return md5($this->signKey.':'.$this->filename.'?'.http_build_query($this->params));
    }
}
