<?php

namespace Glide\Exceptions;

class ManipulationException extends \Exception
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
