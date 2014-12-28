<?php

namespace Glide;

class Token
{
    private $filename;
    private $params;
    private $signKey;

    public function __construct($filename, $params = [], $signKey = null)
    {
        $this->setFilename($filename);
        $this->setParams($params);
        $this->setSignKey($signKey);
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setParams($params)
    {
        ksort($params);

        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function getSignKey()
    {
        return $this->signKey;
    }

    public function generate()
    {
        return md5($this->signKey . ':' . $this->filename . '?' . http_build_query($this->params));
    }
}
