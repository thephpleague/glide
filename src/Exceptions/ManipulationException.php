<?php

namespace Glide\Exceptions;

use Exception;
use Glide\Interfaces\ErrorPageException;

class ManipulationException extends Exception implements ErrorPageException
{
    private $errors;

    public function __construct(Array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function generateErrorPage()
    {
        $page = new ErrorPage($this);
        $page->setTitle('Invalid Manipulation');
        $page->setErrors($this->errors);
        return $page->generate();
    }
}
