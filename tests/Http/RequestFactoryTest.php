<?php

namespace League\Glide\Http;

class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServer()
    {
        $this->assertInstanceOf('League\Glide\Http\RequestFactory', new RequestFactory('image.jpg'));
    }

    public function testGetRequest()
    {
        $request = new RequestFactory('image.jpg');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request->getRequest());
    }

    public function testGetRequestWithParams()
    {
        $request = new RequestFactory('image.jpg', ['w' => 100]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request->getRequest());
        $this->assertEquals('image.jpg', $request->getRequest()->getPathInfo());
        $this->assertSame('100', $request->getRequest()->get('w'));
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', RequestFactory::create('image.jpg'));
    }
}
