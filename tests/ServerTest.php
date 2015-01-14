<?php

namespace League\Glide;

use League\Glide\Factories\Request;
use Mockery;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    private $server;

    public function setUp()
    {
        $this->server = new Server(
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('League\Glide\Interfaces\API')
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Server', $this->server);
    }

    public function testSetSource()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface'));
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getSource());
    }

    public function testGetSource()
    {
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getSource());
    }

    public function testSetCache()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface'));
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getCache());
    }

    public function testGetCache()
    {
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->server->getCache());
    }

    public function testSetAPI()
    {
        $api = Mockery::mock('League\Glide\Interfaces\API');
        $this->server->setApi($api);
        $this->assertInstanceOf('League\Glide\Interfaces\API', $this->server->getApi());
    }

    public function testGetAPI()
    {
        $this->assertInstanceOf('League\Glide\Interfaces\API', $this->server->getApi());
    }

    public function testSetBaseUrl()
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('img/', $this->server->getBaseUrl());
    }

    public function testGetBaseUrl()
    {
        $this->assertEquals('', $this->server->getBaseUrl());
    }

    public function testResolveRequestObject()
    {
        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Request',
            $this->server->resolveRequestObject(
                [Request::create('image.jpg', ['w' => '100'])]
            )
        );

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Request',
            $this->server->resolveRequestObject(
                ['image.jpg', ['w' => '100']]
            )
        );
    }

    public function testResolveRequestObjectWithInvalidArgs()
    {
        $this->setExpectedException('InvalidArgumentException', 'Not a valid filename or Request object.');

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Request',
            $this->server->resolveRequestObject([])
        );
    }

    public function testGetSourceFilename()
    {
        $this->assertEquals('image.jpg', $this->server->getSourceFilename('image.jpg'));
        $this->assertEquals('image.jpg', $this->server->getSourceFilename(Request::create('image.jpg')));
    }

    public function testGetSourceFilenameWithBaseUrl()
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('image.jpg', $this->server->getSourceFilename('img/image.jpg'));
    }

    public function testGetSourceFilenameWithMissingPath()
    {
        $this->setExpectedException(
            'League\Glide\Exceptions\ImageNotFoundException',
            'Image filename missing.'
        );

        $this->server->getSourceFilename('');
    }

    public function testGetCacheFilename()
    {
        $this->assertEquals(
            'e863e008b6f09807c3b0aa3805bc9c63',
            $this->server->getCacheFilename('image.jpg', ['w' => '100'])
        );
    }

    public function testSourceFileExists()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->with('image.jpg')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->sourceFileExists('image.jpg'));
    }

    public function testCacheFileExists()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->with('75094881e9fd2b93063d6a5cb083091c')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->cacheFileExists('image.jpg'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutputImage()
    {
        ob_start();

        $file = tmpfile();
        fwrite($file, 'content');

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) use ($file) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('readStream')->andReturn($file);
        }));

        $response = $this->server->outputImage('image.jpg');
        $content = ob_get_clean();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $response);
        $this->assertEquals('content', $content);
    }

    public function testGetImageResponse()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('readStream')->andReturn(tmpfile());
        }));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $this->server->getImageResponse('image.jpg'));
    }

    public function testMakeImageFromCache()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $this->server->makeImage('image.jpg'));
    }

    public function testMakeImageFromSourceThatDoesNotExist()
    {
        $this->setExpectedException(
            'League\Glide\Exceptions\ImageNotFoundException',
            'Could not find the image `image.jpg`.'
        );

        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $this->server->makeImage('image.jpg'));
    }

    public function testMakeImageFromSource()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
            $mock->shouldReceive('write')->with('75094881e9fd2b93063d6a5cb083091c', 'content')->once();
        }));

        $this->server->setApi(Mockery::mock('League\Glide\Interfaces\API', function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $this->server->makeImage('image.jpg'));
    }
}
