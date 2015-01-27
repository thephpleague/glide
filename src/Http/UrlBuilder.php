<?php

namespace League\Glide\Http;

use InvalidArgumentException;

class UrlBuilder
{
    /**
     * Base URL prefixed to generated URL.
     * @var string
     */
    protected $baseUrl;

    /**
     * HTTP signature used to sign URLs.
     * @var SignatureInterface
     */
    protected $httpSignature;

    /**
     * Create UrlBuilder instance.
     * @param string                  $baseUrl       Base URL prefixed to generated URL.
     * @param SignatureInterface|null $httpSignature HTTP signature used to sign URLs.
     */
    public function __construct($baseUrl = '', SignatureInterface $httpSignature = null)
    {
        $this->baseUrl = $baseUrl;
        $this->httpSignature = $httpSignature;
    }

    /**
     * Get the URL.
     * @param  string $path   The resource path.
     * @param  array  $params The manipulation parameters.
     * @return string The URL.
     */
    public function getUrl($path, array $params = [])
    {
        $parts = parse_url(trim($this->baseUrl, '/').'/'.trim($path, '/'));

        if ($parts === false) {
            throw new InvalidArgumentException('Not a valid path.');
        }

        $parts['path'] = '/'.trim($parts['path'], '/');

        if ($this->httpSignature) {
            $params = $this->httpSignature->addSignature($parts['path'], $params);
        }

        return $this->buildUrl($parts, $params);
    }

    /**
     * Build the URL.
     * @param  array  $parts  The URL parts.
     * @param  array  $params The manipulation parameters.
     * @return string The built URL.
     */
    protected function buildUrl($parts, $params)
    {
        $url = '';

        if (isset($parts['scheme']) and isset($parts['host'])) {
            $url .= $parts['scheme'].'://'.$parts['host'];

            if (isset($parts['port'])) {
                $url .= ':'.$parts['port'];
            }
        }

        $url .= $parts['path'];

        if (count($params)) {
            $url .= '?'.http_build_query($params);
        }

        return $url;
    }
}
