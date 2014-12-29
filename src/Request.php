<?php

namespace Glide;

use Glide\Exceptions\InvalidTokenException;

class Request
{
    private $filename;
    private $params;
    private $signKey;

    public function __construct($filename, $params = [], $signKey = null)
    {
        $this->setFilename($filename);
        $this->setSignKey($signKey);
        $this->setParams($params);
    }

    public function __get($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
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

    public function getSignKey()
    {
        return $this->signKey;
    }

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

    public function getParams()
    {
        return $this->params;
    }

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

    public function getHash()
    {
        return md5($this->filename . '?' . http_build_query($this->params));
    }
}
