<?php

namespace League\Glide\Urls;

use InvalidArgumentException;
use League\Glide\Signatures\SignatureInterface;
use League\Uri\Schemes\Http as HttpUri;
use League\Uri\Modifiers\AppendSegment;
use League\Uri\Modifiers\AddLeadingSlash;
use Psr\Http\Message\UriInterface;

class UrlBuilder
{
    /**
     * The base URI
     * @var UriInterface
     */
    protected $baseUrl;

    /**
     * The HTTP signature used to sign URLs.
     * @var SignatureInterface
     */
    protected $signature;

    /**
     * Create UrlBuilder instance.
     * @param string                  $baseUrl   The base URL.
     * @param SignatureInterface|null $signature The HTTP signature used to sign URLs.
     */
    public function __construct($baseUrl = '', SignatureInterface $signature = null)
    {
        $this->setBaseUrl($baseUrl);
        $this->setSignature($signature);
    }

    /**
     * Set the base URL.
     * @param string $baseUrl The base URL.
     */
    public function setBaseUrl($baseUrl)
    {
        static $addLeadingSlash;
        if (!$addLeadingSlash) {
            $addLeadingSlash = new AddLeadingSlash();
        }

        $this->baseUrl = $addLeadingSlash->__invoke(HttpUri::createFromString($baseUrl));
    }

    /**
     * Set the HTTP signature.
     * @param SignatureInterface|null $signature The HTTP signature used to sign URLs.
     */
    public function setSignature(SignatureInterface $signature = null)
    {
        $this->signature = $signature;
    }

    /**
     * Get the URL.
     * @param  string $path   The resource path.
     * @param  array  $params The manipulation parameters.
     * @return string The URL.
     */
    public function getUrl($path, array $params = [])
    {
        $uri = (new AppendSegment($path))->__invoke($this->baseUrl);
        if ($this->signature) {
            $params = $this->signature->addSignature($uri->getPath(), $params);
        }

        return (string) $uri->withQuery(http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    }
}
