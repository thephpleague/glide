<?php

namespace League\Glide;

use Mockery;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    private $server;

    public function setUp()
    {
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');

        $responseFactory = Mockery::mock('League\Glide\Responses\ResponseFactoryInterface');
        $responseFactory
            ->shouldReceive('create')
            ->andReturn($response)
            ->shouldReceive('send')
            ->andReturnUsing(function () {
                echo 'content';
            });

        $this->server = new Server(
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('League\Glide\Api\ApiInterface'),
            $responseFactory
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

    public function testSetSourcePathPrefix()
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('img', $this->server->getSourcePathPrefix());
    }

    public function testGetSourcePathPrefix()
    {
        $this->assertEquals('', $this->server->getSourcePathPrefix());
    }

    public function testGetSourcePath()
    {
        $this->assertEquals('image.jpg', $this->server->getSourcePath('image.jpg'));
    }

    public function testGetSourcePathWithBaseUrl()
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('image.jpg', $this->server->getSourcePath('img/image.jpg'));
    }

    public function testGetSourcePathWithPrefix()
    {
        $this->server->setSourcePathPrefix('img/');
        $this->assertEquals('img/image.jpg', $this->server->getSourcePath('image.jpg'));
    }

    public function testGetSourcePathWithMissingPath()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FileNotFoundException',
            'Image path missing.'
        );

        $this->server->getSourcePath('');
    }

    public function testGetSourcePathWithEncodedEntities()
    {
        $this->assertEquals('an image.jpg', $this->server->getSourcePath('an%20image.jpg'));
    }

    public function testSourceFileExists()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->with('image.jpg')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->sourceFileExists('image.jpg'));
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

    public function testSetCachePathPrefix()
    {
        $this->server->setCachePathPrefix('img/');
        $this->assertEquals('img', $this->server->getCachePathPrefix());
    }

    public function testGetCachePathPrefix()
    {
        $this->assertEquals('', $this->server->getCachePathPrefix());
    }

    public function testGetCachePath()
    {
        $this->assertEquals(
            'image.jpg/e863e008b6f09807c3b0aa3805bc9c63',
            $this->server->getCachePath('image.jpg', ['w' => '100'])
        );
    }

    public function testGetCachePathWithPrefix()
    {
        $this->server->setCachePathPrefix('img/');
        $this->assertEquals('img/image.jpg/75094881e9fd2b93063d6a5cb083091c', $this->server->getCachePath('image.jpg', []));
    }

    public function testCacheFileExists()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->with('image.jpg/75094881e9fd2b93063d6a5cb083091c')->andReturn(true)->once();
        }));

        $this->assertTrue($this->server->cacheFileExists('image.jpg', []));
    }

    public function testSetAPI()
    {
        $api = Mockery::mock('League\Glide\Api\ApiInterface');
        $this->server->setApi($api);
        $this->assertInstanceOf('League\Glide\Api\ApiInterface', $this->server->getApi());
    }

    public function testGetAPI()
    {
        $this->assertInstanceOf('League\Glide\Api\ApiInterface', $this->server->getApi());
    }

    public function testSetBaseUrl()
    {
        $this->server->setBaseUrl('img/');
        $this->assertEquals('img', $this->server->getBaseUrl());
    }

    public function testGetBaseUrl()
    {
        $this->assertEquals('', $this->server->getBaseUrl());
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
            $mock->shouldReceive('getTimestamp')->andReturn(time());
            $mock->shouldReceive('readStream')->andReturn($file);
        }));

        $response = $this->server->outputImage('image.jpg', []);
        $content = ob_get_clean();

        $this->assertNull($response);
        $this->assertEquals('content', $content);
    }

    public function testGetImageResponse()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('getTimestamp')->andReturn(time());
            $mock->shouldReceive('readStream')->andReturn(tmpfile());
        }));

        $this->assertInstanceOf(
            'Psr\Http\Message\ResponseInterface',
            $this->server->getImageResponse('image.jpg', [])
        );
    }

    public function testMakeImageFromCache()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertEquals(
            'image.jpg/75094881e9fd2b93063d6a5cb083091c',
            $this->server->makeImage('image.jpg', [])
        );
    }

    public function testMakeImageFromSourceThatDoesNotExist()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FileNotFoundException',
            'Could not find the image `image.jpg`.'
        );

        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithUnreadableSource()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FilesystemException',
            'Could not read the image `image.jpg`.'
        );

        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn(false)->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithUnwritableCache()
    {
        $this->setExpectedException(
            'League\Glide\Filesystem\FilesystemException',
            'Could not write the image `image.jpg/75094881e9fd2b93063d6a5cb083091c`.'
        );

        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
            $mock->shouldReceive('write')->andReturn(false)->once();
        }));

        $this->server->setApi(Mockery::mock('League\Glide\Api\ApiInterface', function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->server->makeImage('image.jpg', []);
    }

    public function testMakeImageWithExistingCacheFile()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
            $mock->shouldReceive('write')->andThrow(new \League\Flysystem\FileExistsException('75094881e9fd2b93063d6a5cb083091c'));
        }));

        $this->server->setApi(Mockery::mock('League\Glide\Api\ApiInterface', function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->assertEquals(
            'image.jpg/75094881e9fd2b93063d6a5cb083091c',
            $this->server->makeImage('image.jpg', [])
        );
    }

    public function testMakeImageFromSource()
    {
        $this->server->setSource(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true)->once();
            $mock->shouldReceive('read')->andReturn('content')->once();
        }));

        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(false)->once();
            $mock->shouldReceive('write')->with('image.jpg/75094881e9fd2b93063d6a5cb083091c', 'content')->once();
        }));

        $this->server->setApi(Mockery::mock('League\Glide\Api\ApiInterface', function ($mock) {
            $mock->shouldReceive('run')->andReturn('content')->once();
        }));

        $this->assertEquals(
            'image.jpg/75094881e9fd2b93063d6a5cb083091c',
            $this->server->makeImage('image.jpg', [])
        );
    }
}
