<?php

namespace League\Glide;

use Mockery;

class ServerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\ImageServerFactory', new ImageServerFactory());
    }

    public function testGetServer()
    {
        $server = new ImageServerFactory([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'response' => Mockery::mock('League\Glide\Responses\ResponseFactoryInterface'),
        ]);

        $this->assertInstanceOf('League\Glide\Server', $server->getServer());
    }

    public function testGetSource()
    {
        $server = new ImageServerFactory([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getSource());

        $server = new ImageServerFactory([
            'source' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getSource());
    }

    public function testGetSourceWithNoneSet()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'A "source" file system must be set.'
        );

        $server = new ImageServerFactory();
        $server->getSource();
    }

    public function testGetSourcePathPrefix()
    {
        $server = new ImageServerFactory([
            'source_path_prefix' => 'source',
        ]);

        $this->assertSame('source', $server->getSourcePathPrefix());
    }

    public function testGetCache()
    {
        $server = new ImageServerFactory([
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getCache());

        $server = new ImageServerFactory([
            'cache' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getCache());
    }

    public function testGetCacheWithNoneSet()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'A "cache" file system must be set.'
        );

        $server = new ImageServerFactory();
        $server->getCache();
    }

    public function testGetCachePathPrefix()
    {
        $server = new ImageServerFactory([
            'cache_path_prefix' => 'cache',
        ]);

        $this->assertSame('cache', $server->getCachePathPrefix());
    }

    public function testGetGroupCacheInFolders()
    {
        $server = new ImageServerFactory();

        $this->assertTrue($server->getGroupCacheInFolders());

        $server = new ImageServerFactory([
            'group_cache_in_folders' => false,
        ]);

        $this->assertFalse($server->getGroupCacheInFolders());
    }

    public function testGetCacheWithFileExtensions()
    {
        $server = new ImageServerFactory();

        $this->assertFalse($server->getCacheWithFileExtensions());

        $server = new ImageServerFactory([
            'cache_with_file_extensions' => true,
        ]);

        $this->assertTrue($server->getCacheWithFileExtensions());
    }

    public function testGetWatermarks()
    {
        $server = new ImageServerFactory([
            'watermarks' => Mockery::mock('League\Flysystem\FilesystemInterface'),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getWatermarks());

        $server = new ImageServerFactory([
            'watermarks' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $server->getWatermarks());
    }

    public function testGetWatermarksPathPrefix()
    {
        $server = new ImageServerFactory([
            'watermarks_path_prefix' => 'watermarks',
        ]);

        $this->assertSame('watermarks', $server->getWatermarksPathPrefix());
    }

    public function testGetApi()
    {
        $server = new ImageServerFactory();

        $this->assertInstanceOf('League\Glide\Api\Api', $server->getApi());
    }

    public function testGetImageManager()
    {
        $server = new ImageServerFactory([
            'driver' => 'imagick',
        ]);
        $imageManager = $server->getImageManager();

        $this->assertInstanceOf('Intervention\Image\ImageManager', $imageManager);
        $this->assertSame('imagick', $imageManager->config['driver']);
    }

    public function testGetImageManagerWithNoneSet()
    {
        $server = new ImageServerFactory();
        $imageManager = $server->getImageManager();

        $this->assertInstanceOf('Intervention\Image\ImageManager', $imageManager);
        $this->assertSame('gd', $imageManager->config['driver']);
    }

    public function testGetManipulators()
    {
        $server = new ImageServerFactory();
        $manipulators = $server->getManipulators();

        $this->assertInternalType('array', $manipulators);
        $this->assertInstanceOf('League\Glide\Manipulators\ManipulatorInterface', $manipulators[0]);
    }

    public function testGetMaxImageSize()
    {
        $server = new ImageServerFactory([
            'max_image_size' => 100,
        ]);

        $this->assertSame(100, $server->getMaxImageSize());
    }

    public function testGetDefaults()
    {
        $defaults = [
            'fm' => 'jpg',
        ];

        $server = new ImageServerFactory([
            'defaults' => $defaults,
        ]);

        $this->assertSame($defaults, $server->getDefaults());
    }

    public function testGetPresets()
    {
        $presets = [
            'small' => [
                'w' => 500,
            ],
        ];

        $server = new ImageServerFactory([
            'presets' => $presets,
        ]);

        $this->assertSame($presets, $server->getPresets());
    }

    public function testGetBaseUrl()
    {
        $server = new ImageServerFactory([
            'base_url' => 'img/',
        ]);

        $this->assertSame('img/', $server->getBaseUrl());
    }

    public function testGetResponseFactory()
    {
        $server = new ImageServerFactory([
            'response' => Mockery::mock('League\Glide\Responses\ResponseFactoryInterface'),
        ]);

        $this->assertInstanceOf('League\Glide\Responses\ResponseFactoryInterface', $server->getResponseFactory());
    }

    public function testGetResponseFactoryWithNoneSet()
    {
        $server = new ImageServerFactory();

        $this->assertNull($server->getResponseFactory());
    }

    public function testCreate()
    {
        $server = ImageServer::create([
            'source' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'cache' => Mockery::mock('League\Flysystem\FilesystemInterface'),
            'response' => Mockery::mock('League\Glide\Responses\ResponseFactoryInterface'),
        ]);

        $this->assertInstanceOf('League\Glide\Server', $server);
    }
}
