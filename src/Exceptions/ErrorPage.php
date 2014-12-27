<?php

namespace Glide\Exceptions;

class ErrorPage
{
    private $exception;
    private $title;
    private $errors = [];

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setErrors(Array $errors)
    {
        $this->errors = $errors;
    }

    public function generate()
    {
        ob_start();
        include __DIR__ . '/views/error_page.tpl';
        return ob_get_clean();
    }
}
