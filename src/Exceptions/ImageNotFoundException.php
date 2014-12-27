<?php

namespace Glide\Exceptions;

class ImageNotFoundException extends \Exception
{
    public function generateErrorPage()
    {
        $page = new ErrorPage($this);
        $page->setTitle('Image Not Found');
        return $page->generate();
    }
}
