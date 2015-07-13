<?php

namespace League\Glide\Responses;

use Mockery;

class SymfonyResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf(
            'League\Glide\Responses\SymfonyResponseFactory',
            new SymfonyResponseFactory()
        );
    }

    public function testCreate()
    {
        $this->cache = Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg')->once();
            $mock->shouldReceive('getSize')->andReturn(0)->once();
            $mock->shouldReceive('readStream');
        });

        $factory = new SymfonyResponseFactory();
        $response = $factory->create($this->cache, '');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertEquals('0', $response->headers->get('Content-Length'));
        $this->assertContains(gmdate('D, d M Y H:i', strtotime('+1 years')), $response->headers->get('Expires'));
        $this->assertEquals('max-age=31536000, public', $response->headers->get('Cache-Control'));
    }
}
