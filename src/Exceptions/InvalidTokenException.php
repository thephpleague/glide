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
        return $page->generate();
    }
}
