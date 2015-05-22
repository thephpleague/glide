<?php

namespace League\Glide\Http;

use Symfony\Component\HttpFoundation\Request;

interface SignatureInterface
{
    /**
     * Add an HTTP signature to manipulation parameters.
     * @param  string $path   The resource path.
     * @param  array  $params The manipulation parameters.
     * @return array  The updated manipulation parameters.
     */
    public function addSignature($path, array $params);

    /**
     * Validate a request signature.
     * @param  mixed
     * @throws SignatureException
     */
    public function validateRequest();
}
