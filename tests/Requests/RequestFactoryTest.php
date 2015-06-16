<?php

namespace League\Glide\Requests;

use Symfony\Component\HttpFoundation\Request;

class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWithRequestObject()
    {
        $request = RequestFactory::create([Request::createFromGlobals()]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertSame('/', $request->getPathInfo());
    }

    public function testCreateWithPath()
    {
        $request = RequestFactory::create(['image.jpg']);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertSame('image.jpg', $request->getPathInfo());
    }

    public function testCreateWithPathAndParams()
    {
        $request = RequestFactory::create(['image.jpg', ['w' => 100]]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertSame('image.jpg', $request->getPathInfo());
        $this->assertSame(100, $request->get('w'));
    }

    public function testCreateWithPathAndDefaultManipulations()
    {
        $request = RequestFactory::create(['image.jpg'], ['w' => 100]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertSame('image.jpg', $request->getPathInfo());
        $this->assertSame(100, $request->get('w'));
    }

    public function testCreateWithPathAndParamsAndDefaultManipulations()
    {
        $request = RequestFactory::create(['image.jpg', ['w' => 200]], ['w' => 100]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
        $this->assertSame('image.jpg', $request->getPathInfo());
        $this->assertSame(200, $request->get('w'));
    }

    public function testInvalidArgs()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Not a valid path/params combination or Request object.'
        );

        RequestFactory::create([]);
    }
}
