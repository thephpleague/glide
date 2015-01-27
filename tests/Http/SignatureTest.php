<?php

namespace League\Glide\Http;

class SignatureTest extends \PHPUnit_Framework_TestCase
{
    private $httpSignature;

    public function setUp()
    {
        $this->httpSignature = new Signature('example');
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Http\Signature', $this->httpSignature);
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
                RequestFactory::create('image.jpg', ['w' => '100', 's' => '9978a40f1fc75fa64ac92ea9baf16ff3'])
            )
        );
    }

    public function testValidateRequestWithMissingSignature()
    {
        $this->setExpectedException('League\Glide\Http\SignatureException', 'Signature is missing.');

        $this->httpSignature->validateRequest(
            RequestFactory::create('image.jpg', ['w' => '100'])
        );
    }

    public function testValidateRequestWithInvalidSignature()
    {
        $this->setExpectedException('League\Glide\Http\SignatureException', 'Signature is not valid.');

        $this->httpSignature->validateRequest(
            RequestFactory::create('image.jpg', ['w' => '100', 's' => 'invalid'])
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
