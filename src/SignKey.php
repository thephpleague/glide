<?php

namespace Glide;

use Glide\Exceptions\InvalidTokenException;

class SignKey
{
    /**
     * Secret key used to secure URLs.
     * @var string|null
     */
    private $signKey;

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
     * @param  Array  $params   Manipulation parameters.
     * @return string Generated secure token.
     */
    public function getToken($filename, Array $params = [])
    {
        if (isset($params['token'])) {
            unset($params['token']);
        }

        ksort($params);

        return md5($this->signKey.':'.$filename.'?'.http_build_query($params));
    }

    /**
     * Validate a request against this sign key.
     * @param Request $request The request object.
     */
    public function validateRequest(Request $request)
    {
        if (is_null($request->getParam('token'))) {
            throw new InvalidTokenException('Sign token missing.');
        }

        if ($request->getParam('token') !== $this->getToken($request->getFilename(), $request->getParams())) {
            throw new InvalidTokenException('Sign token invalid.');
        }
    }
}
