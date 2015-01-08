<?php

namespace League\Glide\Factories;

class UrlBuilder
{
    /**
     * Create UrlBuilder instance.
     * @param  string                   $baseUrl URL prefixed to generated URL.
     * @param  string                   $signKey Secret key used to secure URLs.
     * @return \League\Glide\UrlBuilder The UrlBuilder instance.
     */
    public static function create($baseUrl, $signKey = null)
    {
        $httpSignature = null;

        if ($signKey) {
            $httpSignature = HttpSignature::create($signKey);
        }

        return new \League\Glide\UrlBuilder($baseUrl, $httpSignature);
    }
}
