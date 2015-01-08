<?php

namespace League\Glide\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface HttpSignature
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
     * @param  Request                   $request The request object.
     * @throws InvalidSignatureException
     */
    public function validateRequest(Request $request);
}
