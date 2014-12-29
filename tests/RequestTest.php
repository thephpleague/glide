<?php

namespace Glide;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    private $request;

    public function setUp()
    {
        $this->request = new Request('image.jpg');
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Request', $this->request);
    }

    public function testSetFilename()
    {
        $this->request->setFilename('test.jpg');
        $this->assertEquals('test.jpg', $this->request->getFilename());
    }

    public function testGetFilename()
    {
        $this->assertEquals('image.jpg', $this->request->getFilename());
    }

    public function testSetSignKey()
    {
        $this->request->setSignKey('example');
        $this->assertEquals('example', $this->request->getSignKey());
    }

    public function testGetSignKey()
    {
        $this->assertEquals(null, $this->request->getSignKey());
    }

    public function testSetParams()
    {
        $this->request->setParams(['w' => 100]);
        $this->assertEquals(100, $this->request->w);
    }

    public function testSetParamsWithToken()
    {
        $this->request->setParams(['w' => 100, 'token' => '']);
        $this->assertEquals(['w' => 100], $this->request->getParams());
    }

    public function testSetParamsWithNoToken()
    {
        $this->setExpectedException('Glide\Exceptions\InvalidTokenException');

        $this->request->setSignKey('example');
        $this->request->setParams(['w' => 100]);
    }

    public function testSetParamsWithInvalidToken()
    {
        $this->setExpectedException('Glide\Exceptions\InvalidTokenException');

        $this->request->setSignKey('example');
        $this->request->setParams(['w' => 100, 'token' => 'invalid']);
    }

    public function testGetParams()
    {
        $this->assertEquals([], $this->request->getParams());
    }

    public function testGetParam()
    {
        $this->request->setParams(['w' => 100]);
        $this->assertEquals(100, $this->request->w);
    }

    public function testGetHash()
    {
        $this->assertEquals('75094881e9fd2b93063d6a5cb083091c', $this->request->getHash());
    }
}
