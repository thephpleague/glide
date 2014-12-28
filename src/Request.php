<?php

namespace Glide;

use Glide\Exceptions\InvalidTokenException;

class Request
{
    private $filename;
    private $params;
    private $paramToken;
    private $signKey;

    public function __construct($filename, Array $params, $signKey = null)
    {
        $this->setFilename($filename);
        $this->setSignKey($signKey);
        $this->setParams($params);
        $this->validateToken();
    }

    public function setFilename($filename)
    {
        $this->filename = ltrim($filename, '/');
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function setParams(Array $params)
    {
        if (isset($params['token'])) {
            $this->paramToken = $params['token'];
            unset($params['token']);
        }

        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function validateToken()
    {
        if (is_null($this->signKey)) {
            return true;
        }

        if (!isset($this->paramToken)) {
            throw new InvalidTokenException('Signing token is missing.');
        }

        if ($this->paramToken !== $this->getToken()) {
            throw new InvalidTokenException('Invalid signing token.');
        }

        return true;
    }

    public function getToken()
    {
        return (new Token($this->filename, $this->params, $this->signKey))->generate();
    }

    public function getHash()
    {
        return md5($this->filename . '?' . http_build_query($this->params));
    }

    public function __get($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
    }
}
