<?php

namespace League\Glide\Signatures;

class SignatureFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('League\Glide\Signatures\Signature', SignatureFactory::create('example'));
    }
}
