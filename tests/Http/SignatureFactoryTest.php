<?php

namespace League\Glide\Http;

class SignatureFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('League\Glide\Http\Signature', SignatureFactory::create('example'));
    }
}
