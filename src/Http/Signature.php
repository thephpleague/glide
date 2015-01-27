<?php

namespace League\Glide\Http;

use Symfony\Component\HttpFoundation\Request;

class Signature implements SignatureInterface
{
    /**
     * Secret key used to generate signature.
     * @var string
     */
    protected $signKey;

    /**
     * Create Signature instance.
     * @param string $signKey Secret key used to generate signature.
     */
    public function __construct($signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * Add an HTTP signature to manipulation parameters.
     * @param  string $path   The resource path.
     * @param  array  $params The manipulation parameters.
     * @return array  The updated manipulation parameters.
     */
    public function addSignature($path, array $params)
    {
        return array_merge($params, ['s' => $this->generateSignature($path, $params)]);
    }

    /**
     * Validate a request signature.
     * @param  Request            $request The request object.
     * @throws SignatureException
     */
    public function validateRequest(Request $request)
    {
        if (is_null($request->get('s'))) {
            throw new SignatureException('Signature is missing.');
        }

        if ($request->get('s') !== $this->generateSignature($request->getPathInfo(), $request->query->all())) {
            throw new SignatureException('Signature is not valid.');
        }
    }

    /**
     * Generate an HTTP signature.
     * @param  string $path   The resource path.
     * @param  array  $params The manipulation parameters.
     * @return string The generated HTTP signature.
     */
    public function generateSignature($path, array $params)
    {
        unset($params['s']);
        ksort($params);

        return md5($this->signKey.':'.$path.'?'.http_build_query($params));
    }
}
