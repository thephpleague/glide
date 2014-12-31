<?php

namespace Glide;

class UrlBuilder
{
    /**
     * URL prefixed to generated URL.
     * @var string
     */
    private $baseUrl;

    /**
     * Signing key used to secure URLs.
     * @var null|string
     */
    private $signKey;

    /**
     * Create UrlBuilder instance.
     * @param string      $baseUrl URL prefixed to generated URL.
     * @param string|null $signKey Signing key used to secure URLs.
     */
    public function __construct($baseUrl = '', $signKey = null)
    {
        $this->setBaseUrl($baseUrl);
        $this->setSignKey($signKey);
    }

    /**
     * Set the base URL.
     * @param string $baseUrl URL prefixed to generated URL.
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Get the base URL.
     * @return string URL prefixed to generated URL.
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the signing key.
     * @param string|null $signKey Signing key used to secure URLs.
     */
    public function setSignKey($signKey = null)
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
     * Get the URL.
     * @param  string $filename Unique file identifier.
     * @param  Array  $params   Manipulation parameters.
     * @return string Generated URL.
     */
    public function getUrl($filename, Array $params = [])
    {
        if ($this->signKey) {
            $params = $params + ['token' => $this->getToken($filename, $params)];
        }

        return $this->baseUrl.'/'.$filename.'?'.http_build_query($params);
    }

    /**
     * Get a secure token.
     * @param  string $filename Unique file identifier.
     * @param  Array  $params   Manipulation parameters.
     * @return string Generated secure token.
     */
    public function getToken($filename, Array $params = [])
    {
        return (new Token($filename, $params, $this->signKey))->generate();
    }
}
