<?php

namespace League\Glide\Urls;

use League\Glide\Signatures\SignatureInterface;

class UrlBuilder
{
    /**
     * The base URL.
     */
    protected string $baseUrl;

    /**
     * Whether the base URL is a relative domain.
     */
    protected bool $isRelativeDomain = false;

    /**
     * The HTTP signature used to sign URLs.
     */
    protected ?SignatureInterface $signature = null;

    /**
     * Create UrlBuilder instance.
     *
     * @param string                  $baseUrl   The base URL.
     * @param SignatureInterface|null $signature The HTTP signature used to sign URLs.
     */
    public function __construct(string $baseUrl = '', ?SignatureInterface $signature = null)
    {
        $this->setBaseUrl($baseUrl);
        $this->setSignature($signature);
    }

    /**
     * Set the base URL.
     *
     * @param string $baseUrl The base URL.
     */
    public function setBaseUrl(string $baseUrl): void
    {
        if ('//' === substr($baseUrl, 0, 2)) {
            $baseUrl = 'http:'.$baseUrl;
            $this->isRelativeDomain = true;
        }

        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Set the HTTP signature.
     *
     * @param SignatureInterface|null $signature The HTTP signature used to sign URLs.
     */
    public function setSignature(?SignatureInterface $signature = null): void
    {
        $this->signature = $signature;
    }

    /**
     * Get the URL.
     *
     * @param string $path   The resource path.
     * @param array  $params The manipulation parameters.
     *
     * @return string The URL.
     */
    public function getUrl(string $path, array $params = []): string
    {
        $parts = parse_url($this->baseUrl.'/'.trim($path, '/'));

        if (false === $parts) {
            throw new \InvalidArgumentException('Not a valid path.');
        }

        /** @psalm-suppress PossiblyNullArgument, PossiblyUndefinedArrayOffset */
        $parts['path'] = '/'.trim($parts['path'], '/');

        if ($this->signature) {
            $params = $this->signature->addSignature($parts['path'], $params);
        }

        return $this->buildUrl($parts, $params);
    }

    /**
     * Build the URL.
     *
     * @param array $parts  The URL parts.
     * @param array $params The manipulation parameters.
     *
     * @return string The built URL.
     */
    protected function buildUrl(array $parts, array $params): string
    {
        $url = '';

        if (isset($parts['host'])) {
            if ($this->isRelativeDomain) {
                $url .= '//'.$parts['host'];
            } else {
                $url .= $parts['scheme'].'://'.$parts['host'];
            }

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
