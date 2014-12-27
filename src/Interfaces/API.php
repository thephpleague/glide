<?php

namespace Glide\Interfaces;

use Glide\Request;

interface API
{
    public function validate(Request $request, $source);
    public function run(Request $request, $source);
}
