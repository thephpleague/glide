<?php

namespace League\Glide\Http;

class SignatureFactory
{
    /**
     * Create HttpSignature instance.
     * @param  string        $signKey Secret key used to generate signature.
     * @return HttpSignature The HttpSignature instance.
     */
    public static function create($signKey)
    {
        return new Signature($signKey);
    }
}
