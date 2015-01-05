<?php

namespace League\Glide;

use League\Glide\Exceptions\InvalidTokenException;
use Symfony\Component\HttpFoundation\Request;

class SignKey
{
    /**
     * Secret key used to secure URLs.
     * @var string|null
     */
    protected $signKey;

    /**
     * Create SignKey instance.
     * @param string $signKey Secret key used to secure URLs.
     */
    public function __construct($signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * Get a secure token.
     * @param  string $filename Unique file identifier.
     * @param  array  $params   Manipulation parameters.
     * @return string Generated secure token.
     */
    public function getToken($filename, array $params = [])
    {
        if (isset($params['token'])) {
            unset($params['token']);
        }

        ksort($params);

        return md5($this->signKey.':'.ltrim($filename, '/').'?'.http_build_query($params));
    }

    /**
     * Validate a request against this sign key.
     * @param Request $request The request object.
     */
    public function validateRequest(Request $request)
    {
        if (is_null($request->get('token'))) {
            throw new InvalidTokenException('Sign token missing.');
        }

        if ($request->get('token') !== $this->getToken($request->getPathInfo(), $request->query->all())) {
            throw new InvalidTokenException('Sign token invalid.');
        }
    }
}
