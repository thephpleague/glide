<?php

namespace Glide\Exceptions;

class InvalidTokenException extends \Exception
{
    public function generateErrorPage()
    {
        $page = new ErrorPage($this);
        $page->setTitle('Invalid Signing Token');
        return $page->generate();
    }
}
