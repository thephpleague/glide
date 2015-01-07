<?php

namespace League\Glide\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface HttpSignature
{
    public function addSignature($path, array $params);
    public function validateRequest(Request $request);
}
