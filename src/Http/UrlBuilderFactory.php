<?php

namespace League\Glide\Http;

class UrlBuilderFactory
{
    /**
     * Create UrlBuilder instance.
     * @param  string     $baseUrl URL prefixed to generated URL.
     * @param  string     $signKey Secret key used to secure URLs.
     * @return UrlBuilder The UrlBuilder instance.
     */
    public static function create($baseUrl, $signKey = null)
    {
        $httpSignature = null;

        if ($signKey) {
            $httpSignature = SignatureFactory::create($signKey);
        }

        return new UrlBuilder($baseUrl, $httpSignature);
    }
}
