<?php

namespace League\Glide\Factories;

class HttpSignature
{
    /**
     * Create HttpSignature instance.
     * @param  string                     $signKey Secret key used to generate signature.
     * @return League\Glide\HttpSignature The HttpSignature instance.
     */
    public static function create($signKey)
    {
        return new \League\Glide\HttpSignature($signKey);
    }
}
