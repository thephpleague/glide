<?php

namespace League\Glide;

use League\Glide\Factories\Request;

class SignKeyTest extends \PHPUnit_Framework_TestCase
{
    private $signKey;

    public function setUp()
    {
        $this->signKey = new SignKey('example');
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\SignKey', $this->signKey);
    }

    public function testGetToken()
    {
        $this->assertEquals(
            '9978a40f1fc75fa64ac92ea9baf16ff3',
            $this->signKey->getToken('image.jpg', ['w' => '100', 'token' => '9978a40f1fc75fa64ac92ea9baf16ff3'])
        );
    }

    public function testValidateValidRequest()
    {
        $this->signKey->validateRequest(
            Request::create('image.jpg', ['w' => '100', 'token' => '9978a40f1fc75fa64ac92ea9baf16ff3'])
        );
    }

    public function testValidateRequestWithMissingToken()
    {
        $this->setExpectedException('League\Glide\Exceptions\InvalidTokenException', 'Sign token missing.');

        $this->signKey->validateRequest(
            Request::create('image.jpg', ['w' => '100'])
        );
    }

    public function testValidateRequestWithInvalidToken()
    {
        $this->setExpectedException('League\Glide\Exceptions\InvalidTokenException', 'Sign token invalid.');

        $this->signKey->validateRequest(
            Request::create('image.jpg', ['w' => '100', 'token' => 'invalid'])
        );
    }
}
