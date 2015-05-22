<?php

namespace League\Glide;

use Mockery;

class ServerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServer()
    {
        $this->assertInstanceOf('League\Glide\ServerFactory', new ServerFactory([]));
    }

    public function testGetServer()
    {
        $server = new ServerFactory([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Glide\Server', $server->getServer());
    }

    public function testGetSource()
    {
        $server = new ServerFactory([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getSource());
    }

    public function testGetSourceWithLocalPath()
    {
        $server = new ServerFactory([
            'source' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getSource());
    }

    public function testGetSourceWithInvalidParam()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid `source` parameter.');

        $server = new ServerFactory([]);
        $server->getSource();
    }

    public function testGetCache()
    {
        $server = new ServerFactory([
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getCache());
    }

    public function testGetCacheWithLocalPath()
    {
        $server = new ServerFactory([
            'cache' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getCache());
    }

    public function testGetApi()
    {
        $server = new ServerFactory([]);

        $this->assertInstanceOf('League\Glide\Api\Api', $server->getApi());
    }

    public function testGetBaseUrl()
    {
        $server = new ServerFactory([
            'base_url' => 'img/',
        ]);

        $this->assertEquals('img/', $server->getBaseUrl());
    }

    public function testCreate()
    {
        $server = ServerFactory::create([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);
        $this->assertInstanceOf('League\Glide\Server', $server);
    }
}
