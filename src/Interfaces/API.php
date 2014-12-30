<?php

namespace Glide\Interfaces;

use Glide\Request;

interface API
{
    public function run(Request $request, $source);
}
