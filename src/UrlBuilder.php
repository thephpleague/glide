<?php

namespace League\Glide;

class UrlBuilder
{
    /**
     * URL prefixed to generated URL.
     * @var string
     */
    private $baseUrl;

    /**
     * Secret key used to secure URLs.
     * @var SignKey
     */
    private $signKey;

    /**
     * Create UrlBuilder instance.
     * @param string      $baseUrl URL prefixed to generated URL.
     * @param string|null $signKey Secret key used to secure URLs.
     */
    public function __construct($baseUrl = '', $signKey = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');

        if (!is_null($signKey)) {
            $this->signKey = new SignKey($signKey);
        }
    }

    /**
     * Get the URL.
     * @param  string $filename Unique file identifier.
     * @param  Array  $params   Manipulation parameters.
     * @return string The generated URL.
     */
    public function getUrl($filename, Array $params = [])
    {
        if ($this->signKey) {
            $params = $params + ['token' => $this->signKey->getToken($filename, $params)];
        }

        return $this->baseUrl.'/'.$filename.'?'.http_build_query($params);
    }
}
