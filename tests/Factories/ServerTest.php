<?php

namespace Glide\Factories;

use Mockery;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServer()
    {
        $this->assertInstanceOf('Glide\Factories\Server', new Server([]));
    }

    public function testMake()
    {
        $server = new Server([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('Glide\Server', $server->make());
    }

    public function testGetSource()
    {
        $server = new Server([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getSource());
    }

    public function testGetCache()
    {
        $server = new Server([
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getCache());
    }

    public function testGetApi()
    {
        $server = new Server([]);

        $this->assertInstanceOf('Glide\Api', $server->getApi());
    }

    public function testGetImageManager()
    {
        $server = new Server([
            'driver' => 'imagick',
        ]);

        $this->assertEquals('imagick', $server->getImageManager()->config['driver']);
    }

    public function testGetSignKey()
    {
        $server = new Server([
            'sign_key' => 'example',
        ]);

        $this->assertInstanceOf('Glide\SignKey', $server->getSignKey());
    }

    public function testGetManipulators()
    {
        $server = new Server([]);

        $this->assertInternalType('array', $server->getManipulators());
    }

    public function testGetManipulatorsWithMaxImageSize()
    {
        $server = new Server([
            'max_image_size' => 2000*2000,
        ]);

        $sizeManipulator = null;

        foreach ($server->getManipulators() as $manipulator) {
            if ($manipulator instanceof \Glide\Manipulators\Size) {
                $sizeManipulator = $manipulator;
            }
        }

        $this->assertInstanceOf('Glide\Manipulators\Size', $sizeManipulator);
        $this->assertEquals(2000*2000, $sizeManipulator->getMaxImageSize());
    }
}
