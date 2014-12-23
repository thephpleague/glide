<?php

namespace Glide;

use Exception;

class UrlBuilder
{
    private $baseUrl;
    private $signKey;

    public function __construct($baseUrl, $signKey = null)
    {
        $this->setBaseUrl($baseUrl);
        $this->setSignKey($signKey);
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }

    public function getSignKey()
    {
        return $this->signKey;
    }

    public function getUrl($filename, Array $params = [])
    {
        $params = array_map('trim', $params);
        ksort($params);

        if ($this->signKey) {
            $params = $params + ['token' => $this->getToken($filename, $params)];
        }

        return $this->baseUrl . '/' . $filename . '?' . http_build_query($params);
    }

    private function getToken($filename, $params = [])
    {
        return md5($this->signKey . ':' . $filename . '?' . http_build_query($params));
    }
}
