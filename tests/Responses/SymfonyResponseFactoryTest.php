<?php

namespace League\Glide\Responses;

use Mockery;

class SymfonyResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $cache;

    public function setUp()
    {
        $this->cache = Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('getTimestamp')->andReturn(time());
            $mock->shouldReceive('readStream');
        });
    }

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
        $factory = new SymfonyResponseFactory();
        $response = $factory->create($this->cache, '');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertEquals('0', $response->headers->get('Content-Length'));
        $this->assertEquals(gmdate('D, d M Y H:i:s', strtotime('+1 years')).' GMT', $response->headers->get('Expires'));
        $this->assertEquals('max-age=31536000, public', $response->headers->get('Cache-Control'));
    }
}
