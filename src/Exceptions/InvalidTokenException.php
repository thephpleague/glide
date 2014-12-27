<?php

namespace Glide\Exceptions;

use Exception;
use Glide\Interfaces\ErrorPageException;

class InvalidTokenException extends Exception implements ErrorPageException
{
    public function generateErrorPage()
    {
        $page = new ErrorPage($this);
        $page->setTitle('Invalid Signing Token');
        $page->setErrors([
            'token' => 'The provided token does not match the URL signature.'
        ]);
        return $page->generate();
    }
}
