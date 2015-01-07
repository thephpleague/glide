<?php

namespace League\Glide;

use League\Glide\Factories\Request;

class HttpSignatureTest extends \PHPUnit_Framework_TestCase
{
    private $httpSignature;

    public function setUp()
    {
        $this->httpSignature = new HttpSignature('example');
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\HttpSignature', $this->httpSignature);
    }

    public function testAddSignature()
    {
        $this->assertEquals(
            ['w' => '100', 's' => '9978a40f1fc75fa64ac92ea9baf16ff3'],
            $this->httpSignature->addSignature('image.jpg', ['w' => '100'])
        );
    }

    public function testAddSignatureWithExistingSignature()
    {
        $this->assertEquals(
            ['w' => '100', 's' => '9978a40f1fc75fa64ac92ea9baf16ff3'],
            $this->httpSignature->addSignature('image.jpg', ['w' => '100', 's' => 'existing'])
        );
    }

    public function testValidateRequest()
    {
        $this->assertNull(
            $this->httpSignature->validateRequest(
                Request::create('image.jpg', ['w' => '100', 's' => '9978a40f1fc75fa64ac92ea9baf16ff3'])
            )
        );
    }

    public function testValidateRequestWithMissingSignature()
    {
        $this->setExpectedException('League\Glide\Exceptions\InvalidSignatureException', 'Signature is missing.');

        $this->httpSignature->validateRequest(
            Request::create('image.jpg', ['w' => '100'])
        );
    }

    public function testValidateRequestWithInvalidSignature()
    {
        $this->setExpectedException('League\Glide\Exceptions\InvalidSignatureException', 'Signature is not valid.');

        $this->httpSignature->validateRequest(
            Request::create('image.jpg', ['w' => '100', 's' => 'invalid'])
        );
    }

    public function testGenerateSignature()
    {
        $this->assertEquals(
            '9978a40f1fc75fa64ac92ea9baf16ff3',
            $this->httpSignature->generateSignature('image.jpg', ['w' => '100'])
        );
    }
}
