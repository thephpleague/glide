<?php

namespace League\Glide\Responses;

use Mockery;

class PsrResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $streamCallback = function () {
        };

        $this->assertInstanceOf(
            'League\Glide\Responses\PsrResponseFactory',
            new PsrResponseFactory($response, $streamCallback)
        );
    }

    public function testCreate()
    {
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface', function ($mock) {
            $mock->shouldReceive('withBody')->andReturn($mock)->once();
            $mock->shouldReceive('withHeader')->andReturn($mock)->times(4);
        });

        $streamCallback = function ($stream) {
            return $stream;
        };

        $cache = Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('readStream')->andReturn(
                Mockery::mock('Psr\Http\Message\StreamInterface')
            );
        });

        $factory = new PsrResponseFactory($response, $streamCallback);

        $this->assertInstanceOf(
            'Psr\Http\Message\ResponseInterface',
            $factory->create($cache, 'image.jpg')
        );
    }
}
