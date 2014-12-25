<?php

namespace Glide;

use Glide\API\API;
use Intervention\Image\ImageManager;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use Mockery;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    private $server;

    public function setUp()
    {
        $this->server = new Server(
            new Filesystem(new NullAdapter()),
            new Filesystem(new NullAdapter()),
            new API(new ImageManager())
        );
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Server', $this->server);
    }

    public function testSetSource()
    {
        $this->assertInstanceOf('League\Flysystem\Adapter\NullAdapter', $this->server->getSource()->getAdapter());
        $this->server->setSource(new Filesystem(Mockery::mock('League\Flysystem\Adapter\Local')));
        $this->assertInstanceOf('League\Flysystem\Adapter\Local', $this->server->getSource()->getAdapter());
    }

    public function testGetSource()
    {
        $this->assertInstanceOf('League\Flysystem\Filesystem', $this->server->getSource());
    }

    public function testSetCache()
    {
        $this->assertInstanceOf('League\Flysystem\Adapter\NullAdapter', $this->server->getCache()->getAdapter());
        $this->server->setCache(new Filesystem(Mockery::mock('League\Flysystem\Adapter\Local')));
        $this->assertInstanceOf('League\Flysystem\Adapter\Local', $this->server->getCache()->getAdapter());
    }

    public function testGetCache()
    {
        $this->assertInstanceOf('League\Flysystem\Filesystem', $this->server->getCache());
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

    /**
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\Filesystem', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
            $mock->shouldReceive('getMimetype')->andReturn('image/jpeg');
            $mock->shouldReceive('getSize')->andReturn(0);
            $mock->shouldReceive('readStream')->andReturn(tmpfile());
        }));

        $this->assertInstanceOf('Glide\Request', $this->server->output('image.jpg'));
    }

    public function testGenerate()
    {
        $this->server->setCache(Mockery::mock('League\Flysystem\Filesystem', function ($mock) {
            $mock->shouldReceive('has')->andReturn(true);
        }));

        $this->assertInstanceOf('Glide\Request', $this->server->generate('image.jpg'));
    }
}
