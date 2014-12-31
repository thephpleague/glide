<?php

namespace Glide;

use Glide\Exceptions\InvalidTokenException;

class Request
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
     * Create Request instance.
     * @param string      $filename Unique file identifier.
     * @param Array       $params   Manipulation parameters.
     * @param string|null $signKey  Signing key used to secure URLs.
     */
    public function __construct($filename, Array $params = [], $signKey = null)
    {
        $this->setFilename($filename);
        $this->setSignKey($signKey);
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
        $token = null;

        if (isset($params['token'])) {
            $token = $params['token'];
            unset($params['token']);
        }

        $this->validateToken($token);

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
     * @return string Manipulation parameter.
     */
    public function getParam($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
    }

    /**
     * Set the signing key.
     * @param string|null $signKey Signing key used to secure URLs.
     */
    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * Get the signing key.
     * @return string|null Signing key used to secure URLs.
     */
    public function getSignKey()
    {
        return $this->signKey;
    }

    /**
     * Validate a token against the current request.
     * @param  string $token Supplied secure token.
     * @return null   Returns null if no errors.
     */
    private function validateToken($token)
    {
        if (is_null($this->signKey)) {
            return;
        }

        if (!isset($token)) {
            throw new InvalidTokenException('Signing token is missing.');
        }

        $matchToken = (new Token($this->filename, $this->params, $this->signKey))->generate();

        if ($token !== $matchToken) {
            throw new InvalidTokenException('Invalid signing token.');
        }
    }

    /**
     * Get the request hash.
     * @return string Generated hash.
     */
    public function getHash()
    {
        return md5($this->filename.'?'.http_build_query($this->params));
    }
}
