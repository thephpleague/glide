<?php

namespace League\Glide;

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

    public function testSetSignKey()
    {
        $this->server->setSignKey(new SignKey('example'));
        $this->assertInstanceOf('League\Glide\SignKey', $this->server->getSignKey());
    }

    public function testGetSignKey()
    {
        $this->assertNull($this->server->getSignKey());
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

        $this->assertInstanceOf('League\Glide\Request', $response);
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

    public function testMakeImageWithValidSignKey()
    {
        $this->server->setSignKey(new SignKey('example'));
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf('League\Glide\Request', $this->server->makeImage('image.jpg', ['token' => '0e7aaeb5552fc6135b47fba6377d2a2e']));
    }

    public function testMakeImageWithInvalidSignKey()
    {
        $this->setExpectedException('League\Glide\Exceptions\InvalidTokenException', 'Sign token invalid.');

        $this->server->setSignKey(new SignKey('example'));

        $this->assertInstanceOf('League\Glide\Request', $this->server->makeImage('image.jpg', ['token' => 'invalid']));
    }

    public function testMakeImageFromCache()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf('League\Glide\Request', $this->server->makeImage('image.jpg'));
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

        $this->assertInstanceOf('League\Glide\Request', $this->server->makeImage('image.jpg'));
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

        $this->assertInstanceOf('League\Glide\Request', $this->server->makeImage('image.jpg'));
    }
}
