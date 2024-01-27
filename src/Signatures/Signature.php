<?php

namespace League\Glide\Signatures;

class Signature implements SignatureInterface
{
    /**
     * Secret key used to generate signature.
     */
    protected string $signKey;

    /**
     * Create Signature instance.
     *
     * @param string $signKey Secret key used to generate signature.
     */
    public function __construct(string $signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * Add an HTTP signature to manipulation parameters.
     *
     * @param string $path   The resource path.
     * @param array  $params The manipulation parameters.
     *
     * @return array The updated manipulation parameters.
     */
    public function addSignature(string $path, array $params): array
    {
        return array_merge($params, ['s' => $this->generateSignature($path, $params)]);
    }

    /**
     * Validate a request signature.
     *
     * @param string $path   The resource path.
     * @param array  $params The manipulation params.
     *
     * @throws SignatureException
     */
    public function validateRequest(string $path, array $params): void
    {
        if (!isset($params['s'])) {
            throw new SignatureException('Signature is missing.');
        }

        if ($params['s'] !== $this->generateSignature($path, $params)) {
            throw new SignatureException('Signature is not valid.');
        }
    }

    /**
     * Generate an HTTP signature.
     *
     * @param string $path   The resource path.
     * @param array  $params The manipulation parameters.
     *
     * @return string The generated HTTP signature.
     */
    public function generateSignature(string $path, array $params)
    {
        unset($params['s']);
        ksort($params);

        return md5($this->signKey.':'.ltrim($path, '/').'?'.http_build_query($params));
    }
}
