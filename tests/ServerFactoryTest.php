<?php

declare(strict_types=1);

namespace League\Glide;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Exceptions\InvalidArgumentException;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemOperator;
use League\Glide\Api\Api;
use League\Glide\Api\Decoder;
use League\Glide\Api\Encoder;
use League\Glide\Manipulators\ManipulatorInterface;
use League\Glide\Responses\ResponseFactoryInterface;
use PHPUnit\Framework\TestCase;

class ServerFactoryTest extends TestCase
{
    public function testCreateServerFactory()
    {
        $this->assertInstanceOf(ServerFactory::class, new ServerFactory());
    }

    public function testGetServer()
    {
        $server = new ServerFactory([
            'source' => \Mockery::mock(FilesystemOperator::class),
            'cache' => \Mockery::mock(FilesystemOperator::class),
            'response' => \Mockery::mock(ResponseFactoryInterface::class),
        ]);

        $this->assertInstanceOf(Server::class, $server->getServer());
    }

    public function testGetSource()
    {
        $server = new ServerFactory([
            'source' => \Mockery::mock(FilesystemOperator::class),
        ]);

        $this->assertInstanceOf(FilesystemOperator::class, $server->getSource());

        $server = new ServerFactory([
            'source' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf(FilesystemOperator::class, $server->getSource());
    }

    public function testGetSourceWithNoneSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A "source" file system must be set.');

        $server = new ServerFactory();
        $server->getSource();
    }

    public function testGetSourcePathPrefix()
    {
        $server = new ServerFactory([
            'source_path_prefix' => 'source',
        ]);

        $this->assertSame('source', $server->getSourcePathPrefix());
    }

    public function testGetCache()
    {
        $server = new ServerFactory([
            'cache' => \Mockery::mock(FilesystemOperator::class),
        ]);

        $this->assertInstanceOf(FilesystemOperator::class, $server->getCache());

        $server = new ServerFactory([
            'cache' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf(FilesystemOperator::class, $server->getCache());
    }

    public function testGetCacheWithNoneSet()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A "cache" file system must be set.');

        $server = new ServerFactory();
        $server->getCache();
    }

    public function testGetCachePathPrefix()
    {
        $server = new ServerFactory([
            'cache_path_prefix' => 'cache',
        ]);

        $this->assertSame('cache', $server->getCachePathPrefix());
    }

    public function testGetTempDir()
    {
        $server = new ServerFactory([
            'temp_dir' => __DIR__,
        ]);

        $this->assertSame(__DIR__, $server->getTempDir());
    }

    public function testGetGroupCacheInFolders()
    {
        $server = new ServerFactory();

        $this->assertTrue($server->getGroupCacheInFolders());

        $server = new ServerFactory([
            'group_cache_in_folders' => false,
        ]);

        $this->assertFalse($server->getGroupCacheInFolders());
    }

    public function testGetCacheWithFileExtensions()
    {
        $server = new ServerFactory();

        $this->assertFalse($server->getCacheWithFileExtensions());

        $server = new ServerFactory([
            'cache_with_file_extensions' => true,
        ]);

        $this->assertTrue($server->getCacheWithFileExtensions());
    }

    public function testGetWatermarks()
    {
        $server = new ServerFactory([
            'watermarks' => \Mockery::mock(FilesystemOperator::class),
        ]);

        $this->assertInstanceOf(FilesystemOperator::class, $server->getWatermarks());

        $server = new ServerFactory([
            'watermarks' => sys_get_temp_dir(),
        ]);

        $this->assertInstanceOf(FilesystemOperator::class, $server->getWatermarks());
    }

    public function testGetWatermarksPathPrefix()
    {
        $server = new ServerFactory([
            'watermarks_path_prefix' => 'watermarks',
        ]);

        $this->assertSame('watermarks', $server->getWatermarksPathPrefix());
    }

    public function testGetApi()
    {
        $server = new ServerFactory();

        $this->assertInstanceOf(Api::class, $server->getApi());
    }

    public function testGetImageManagerWithImagick()
    {
        $server = new ServerFactory([
            'driver' => 'imagick',
        ]);
        $imageManager = $server->getImageManager();

        $this->assertInstanceOf(ImageManager::class, $imageManager);
    }

    public function testGetImageManagerWithGd()
    {
        $server = new ServerFactory([
            'driver' => 'gd',
        ]);
        $imageManager = $server->getImageManager();

        $this->assertInstanceOf(ImageManager::class, $imageManager);
    }

    public function testGetImageManagerWithVips()
    {
        $server = new ServerFactory([
            'driver' => 'vips',
        ]);

        if (!class_exists('Intervention\\Image\\Drivers\\Vips\\Driver')) {
            // The vips driver lives in the optional intervention/image-driver-vips package
            $this->expectException(InvalidArgumentException::class);
        }

        $imageManager = $server->getImageManager();

        $this->assertInstanceOf(ImageManager::class, $imageManager);
    }

    public function testGetImageManagerWithNoneSet()
    {
        $server = new ServerFactory();
        $imageManager = $server->getImageManager();

        $this->assertInstanceOf(ImageManager::class, $imageManager);
    }

    public function testGetImageManagerWithDriverOptions()
    {
        $server = new ServerFactory([
            'driver' => [
                'driver' => 'imagick',
                'strip' => true,
            ],
        ]);
        $imageManager = $server->getImageManager();

        $this->assertInstanceOf(ImageManager::class, $imageManager);
        $this->assertTrue($imageManager->driver->config()->strip);
    }

    public function testGetImageManagerWithDriverOptionsAndNoDriverSet()
    {
        $server = new ServerFactory([
            'driver' => [
                'strip' => true,
            ],
        ]);
        $imageManager = $server->getImageManager();

        $this->assertInstanceOf(Driver::class, $imageManager->driver);
        $this->assertTrue($imageManager->driver->config()->strip);
    }

    public function testGetManipulators()
    {
        $server = new ServerFactory();
        $manipulators = $server->getManipulators();

        $this->assertIsArray($manipulators);
        $this->assertInstanceOf(ManipulatorInterface::class, $manipulators[0]);
    }

    public function testGetMaxImageSize()
    {
        $server = new ServerFactory([
            'max_image_size' => 100,
        ]);

        $this->assertSame(100, $server->getMaxImageSize());
    }

    public function testGetDefaults()
    {
        $defaults = [
            'fm' => 'jpg',
        ];

        $server = new ServerFactory([
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

        $server = new ServerFactory([
            'presets' => $presets,
        ]);

        $this->assertSame($presets, $server->getPresets());
    }

    public function testGetBaseUrl()
    {
        $server = new ServerFactory([
            'base_url' => 'img/',
        ]);

        $this->assertSame('img/', $server->getBaseUrl());
    }

    public function testGetResponseFactory()
    {
        $server = new ServerFactory([
            'response' => \Mockery::mock(ResponseFactoryInterface::class),
        ]);

        $this->assertInstanceOf(ResponseFactoryInterface::class, $server->getResponseFactory());
    }

    public function testGetResponseFactoryWithNoneSet()
    {
        $server = new ServerFactory();

        $this->assertNull($server->getResponseFactory());
    }

    public function testCreate()
    {
        $encoder = \Mockery::mock(Encoder::class);
        $decoder = \Mockery::mock(Decoder::class);
        $server = ServerFactory::create([
            'source' => \Mockery::mock(FilesystemOperator::class),
            'cache' => \Mockery::mock(FilesystemOperator::class),
            'response' => \Mockery::mock(ResponseFactoryInterface::class),
            'temp_dir' => __DIR__,
            'encoder' => $encoder,
            'decoder' => $decoder,
        ]);

        $this->assertInstanceOf(Server::class, $server);
        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR, $server->getTempDir());
        $this->assertSame($encoder, $server->getApi()->getEncoder());
        $this->assertSame($decoder, $server->getApi()->getDecoder());
    }
}
