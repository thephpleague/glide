<?php

namespace Glide\API;

use Glide\Request;

interface APIInterface
{
    public function run(Request $request, $imageSource);
}
