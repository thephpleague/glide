<?php

namespace Glide;

use Exception;

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

        if (!$this->validateToken()) {
            throw new InvalidTokenException();
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

    public function setParams(Array $params)
    {
        $params = array_map('trim', $params);
        ksort($params);
        $this->params = $params;

        if (isset($this->params['token'])) {
            $this->paramToken = $this->params['token'];
            unset($this->params['token']);
        }
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
            return false;
        }

        if ($this->paramToken !== $this->getToken()) {
            return false;
        }

        return true;
    }

    public function getToken()
    {
        return md5($this->signKey . ':' . $this->filename . '?' . http_build_query($this->params));
    }

    public function getHash()
    {
        return md5($this->filename . '?' . http_build_query($this->params));
    }
}
