<?php

namespace League\Glide\Factories;

class HttpSignatureTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('League\Glide\HttpSignature', HttpSignature::create('example'));
    }
}
