<?php

namespace League\Glide\Http;

class RequestArgumentsResolverTest extends \PHPUnit_Framework_TestCase
{
    private $resolver;

    public function setUp()
    {
        $this->resolver = new RequestArgumentsResolver();
    }

    public function testCreateRequestArgumentsResolver()
    {
        $this->assertInstanceOf(
            'League\Glide\Http\RequestArgumentsResolver',
            $this->resolver
        );
    }

    public function testRequestObjectArg()
    {
        $request = $this->resolver->getRequest([
            RequestFactory::create('image.jpg', ['w' => 100]),
        ]);

        $this->assertEquals('image.jpg', $request->getPathInfo());
        $this->assertEquals(['w' => 100], $request->query->all());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
    }

    public function testRequestParamsArgs()
    {
        $request = $this->resolver->getRequest([
            'image.jpg', ['w' => 100],
        ]);

        $this->assertEquals('image.jpg', $request->getPathInfo());
        $this->assertEquals(['w' => 100], $request->query->all());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
    }

    public function testInvalidArgs()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Not a valid path or Request object.'
        );

        $this->resolver->getRequest([]);
    }
}
