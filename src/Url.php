<?php

namespace Glide;

class Url
{
    public $filename;
    public $params;

    public function __construct($filename, Array $params)
    {
        $this->filename = $filename;

        $params = array_map('trim', $params);
        ksort($params);
        $this->params = $params;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getHash()
    {
        return md5($this->filename . '?' . http_build_query($this->params));
    }
}
