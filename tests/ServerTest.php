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
        $this->server->setAPI($api);
        $this->assertInstanceOf('Glide\Interfaces\API', $this->server->getAPI());
    }

    public function testGetAPI()
    {
        $this->assertInstanceOf('Glide\Interfaces\API', $this->server->getAPI());
    }

    public function testSetSignKey()
    {
        $this->server->setSignKey('test-key');
        $this->assertEquals('test-key', $this->server->getSignKey());
    }

    public function testGetSignKey()
    {
        $this->assertNull($this->server->getSignKey());
    }

    public function testTest()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf('Glide\Request', $this->server->test('image.jpg'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutput()
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

        $response = $this->server->output('image.jpg');
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

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $this->server->response('image.jpg'));
    }

    public function testGenerate()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\FilesystemInterface', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf('Glide\Request', $this->server->make('image.jpg'));
    }
}
