<?php


namespace League\Glide\Responses;

use Mockery;
use PHPUnit_Framework_TestCase;

class Psr17ResponseFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $cache = Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('readStream')->andReturn(
                Mockery::mock('Psr\Http\Message\StreamInterface')
            );
        });
        $responseFactory = Mockery::mock('Psr\Http\Message\ResponseFactoryInterface', function ($factoryMock) {
            $factoryMock->shouldReceive('createResponse')->andReturn(
                Mockery::mock('Psr\Http\Message\ResponseInterface', function ($responseMock) {
                    $responseMock->shouldReceive('getBody')->andReturn(
                        Mockery::mock('Psr\Http\Message\StreamInterface', function ($streamMock) {
                            $streamMock->shouldReceive('write')->andReturn(0);
                        })
                    );
                    $responseMock->shouldReceive('withHeader')->andReturn($responseMock);
                })
            );
        });

        $factory = new Psr17ResponseFactory($responseFactory);
        $this->assertInstanceOf(
            'Psr\Http\Message\ResponseInterface',
            $factory->create($cache, 'image.jpg')
        );
    }
}