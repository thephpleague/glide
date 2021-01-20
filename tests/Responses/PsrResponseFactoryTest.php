<?php

namespace League\Glide\Responses;

use Mockery;
use PHPUnit\Framework\TestCase;

class PsrResponseFactoryTest extends TestCase
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

        $cache = Mockery::mock('League\Flysystem\FilesystemOperator', function ($mock) {
            $mock->shouldReceive('mimeType')->andReturn('image/jpeg');
            $mock->shouldReceive('fileSize')->andReturn(0);
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
