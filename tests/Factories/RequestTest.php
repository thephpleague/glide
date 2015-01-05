<?php

namespace League\Glide\Factories;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServer()
    {
        $this->assertInstanceOf('League\Glide\Factories\Request', new Request('image.jpg'));
    }

    public function testGetRequest()
    {
        $request = new Request('image.jpg');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request->getRequest());
    }

    public function testGetRequestWithParams()
    {
        $request = new Request('image.jpg', ['w' => 100]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request->getRequest());
        $this->assertEquals('image.jpg', $request->getRequest()->getPathInfo());
        $this->assertSame('100', $request->getRequest()->get('w'));
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', Request::create('image.jpg'));
    }
}
