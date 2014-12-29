<?php

namespace Glide;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    private $token;

    public function setUp()
    {
        $this->token = new Token('image.jpg');
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Token', $this->token);
    }

    public function testSetFilename()
    {
        $this->token->setFilename('test.jpg');
        $this->assertEquals('test.jpg', $this->token->getFilename());
    }

    public function testGetFilename()
    {
        $this->assertEquals('image.jpg', $this->token->getFilename());
    }

    public function testSetParams()
    {
        $this->token->setParams(['w' => 100, 'h' => 200]);
        $this->assertTrue(['h' => 200, 'w' => 100] === $this->token->getParams());
    }

    public function testGetParams()
    {
        $this->assertEquals([], $this->token->getParams());
    }

    public function testSetSignKey()
    {
        $this->token->setSignKey('example');
        $this->assertEquals('example', $this->token->getSignKey());
    }

    public function testGetSignKey()
    {
        $this->assertEquals(null, $this->token->getSignKey());
    }

    public function testGenerate()
    {
        $this->token->setParams(['w' => 100, 'h' => 200]);
        $this->token->setSignKey('example');
        $this->assertEquals('e6f5863c5e5db49d3baf2265ee9e6bec', $this->token->generate());
    }
}
