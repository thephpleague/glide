<?php

namespace League\Glide\Signatures;

use League\Glide\Signatures\SignatureException;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    private $httpSignature;

    public function setUp(): void
    {
        $this->httpSignature = new Signature('example');
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Signatures\Signature', $this->httpSignature);
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
            $this->httpSignature->validateRequest('image.jpg', [
                'w' => '100',
                's' => '9978a40f1fc75fa64ac92ea9baf16ff3',
            ])
        );
    }

    public function testValidateRequestWithLeadingSlash()
    {
        $this->assertNull(
            $this->httpSignature->validateRequest('/image.jpg', [
                'w' => '100',
                's' => '9978a40f1fc75fa64ac92ea9baf16ff3',
            ])
        );
    }

    public function testValidateRequestWithMissingSignature()
    {
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('Signature is missing.');

        $this->httpSignature->validateRequest('image.jpg', [
            'w' => '100',
        ]);
    }

    public function testValidateRequestWithInvalidSignature()
    {
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('Signature is not valid.');

        $this->httpSignature->validateRequest('image.jpg', [
            'w' => '100',
            's' => 'invalid',
        ]);
    }

    public function testGenerateSignature()
    {
        $this->assertEquals(
            '9978a40f1fc75fa64ac92ea9baf16ff3',
            $this->httpSignature->generateSignature('image.jpg', ['w' => '100'])
        );
    }
}
