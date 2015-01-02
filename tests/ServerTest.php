<?php

namespace Glide;

use Mockery;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    private $server;

    public function setUp()
    {
        $this->server = new Server(
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('League\Flysystem\FilesystemInterface'),
            Mockery::mock('Glide\Interfaces\API')
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Server', $this->server);
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
        $api = Mockery::mock('Glide\Interfaces\API');
        $this->server->setApi($api);
        $this->assertInstanceOf('Glide\Interfaces\API', $this->server->getApi());
    }

    public function testGetAPI()
    {
        $this->assertInstanceOf('Glide\Interfaces\API', $this->server->getApi());
    }

    public function testSetSignKey()
    {
        $this->server->setSignKey(new SignKey('example'));
        $this->assertInstanceOf('Glide\SignKey', $this->server->getSignKey());
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

        $this->assertInstanceOf('Glide\Request', $response);
        $this->assertEquals('content', $content);
    }

    public function testResponse()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('readStream')->andReturn(tmpfile());
        }));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $this->server->getImageResponse('image.jpg'));
    }

    public function testGenerate()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf('Glide\Request', $this->server->makeImage('image.jpg'));
    }
}
