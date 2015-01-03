<?php

namespace League\Glide;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    private $request;

    public function setUp()
    {
        $this->request = new Request('image.jpg');
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Request', $this->request);
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

    public function testSetParams()
    {
        $this->request->setParams(['w' => 100]);
        $this->assertEquals(100, $this->request->getParam('w'));
    }

    public function testGetParams()
    {
        $this->assertEquals([], $this->request->getParams());
    }

    public function testGetParam()
    {
        $this->request->setParams(['w' => 100]);
        $this->assertEquals(100, $this->request->getParam('w'));
    }

    public function testGetHash()
    {
        $this->assertEquals('75094881e9fd2b93063d6a5cb083091c', $this->request->getHash());
    }

    public function testGetHashWithToken()
    {
        $this->request->setParams(['token' => 'example']);

        $this->assertEquals('75094881e9fd2b93063d6a5cb083091c', $this->request->getHash());
    }
}
