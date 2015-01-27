<?php

namespace League\Glide\Http\Middleware;

use League\Glide\Http\RequestFactory;
use Mockery;

class ImageServerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $imageServer = new ImageServer(
            Mockery::mock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            Mockery::mock('League\Glide\Server')
        );

        $this->assertInstanceOf('League\Glide\Http\Middleware\ImageServer', $imageServer);
    }

    public function testHandle()
    {
        $app = Mockery::mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $server = Mockery::mock('League\Glide\Server', function ($mock) {
            $mock->shouldReceive('getImageResponse')->andReturn(Mockery::mock('Symfony\Component\HttpFoundation\StreamedResponse'))->once();
        });

        $imageServer = new ImageServer($app, $server);
        $request = RequestFactory::create('image.jpg', ['w' => '100']);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $imageServer->handle($request));
    }
}
