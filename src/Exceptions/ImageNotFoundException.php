<?php

namespace Glide\Exceptions;

use Exception;
use Glide\Interfaces\ErrorPageException;

class ImageNotFoundException extends Exception implements ErrorPageException
{
    public function generateErrorPage()
    {
        $page = new ErrorPage($this);
        $page->setTitle('Image Not Found');
        $page->setErrors([
            '404' => $this->getMessage()
        ]);
        return $page->generate();
    }
}
