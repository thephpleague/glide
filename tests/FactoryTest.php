<?php

namespace Glide;

use Mockery;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServer()
    {
        $server = Factory::server([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('Glide\Server', $server);
    }

    public function testCreateServerWithDifferentDriver()
    {
        $server = Factory::server([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'driver' => 'imagick',
        ]);

        $this->assertEquals('imagick', $server->getApi()->getImageManager()->config['driver']);
    }

    public function testCreateServerWithSignKey()
    {
        $server = Factory::server([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'sign_key' => 'example',
        ]);

        $this->assertInstanceOf('Glide\SignKey', $server->getSignKey());
    }
}
