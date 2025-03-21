<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\ManipulatorInterface;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private Api $api;

    public function setUp(): void
    {
        $this->api = new Api(ImageManager::gd(), []);
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Api::class, $this->api);
    }

    public function testSetImageManager(): void
    {
        $this->api->setImageManager(ImageManager::gd());
        $this->assertInstanceOf(ImageManager::class, $this->api->getImageManager());
    }

    public function testGetImageManager(): void
    {
        $this->assertInstanceOf(ImageManager::class, $this->api->getImageManager());
    }

    public function testSetManipulators(): void
    {
        $this->api->setManipulators([\Mockery::mock(ManipulatorInterface::class)]);
        $manipulators = $this->api->getManipulators();
        $this->assertInstanceOf(ManipulatorInterface::class, $manipulators[0]);
    }

    public function testSetInvalidManipulator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not a valid manipulator.');

        $this->api->setManipulators([new \stdClass()]);
    }

    public function testGetManipulators(): void
    {
        $this->assertEquals([], $this->api->getManipulators());
    }

    public function testGetApiParams(): void
    {
        $manipulator1 = \Mockery::mock(ManipulatorInterface::class, function ($mock) {
            $mock->shouldReceive('getApiParams')->andReturn(['foo', 'bar']);
        });
        $manipulator2 = \Mockery::mock(ManipulatorInterface::class, function ($mock) {
            $mock->shouldReceive('getApiParams')->andReturn(['foo', 'baz']);
        });

        $api = new Api(ImageManager::gd(), [$manipulator1, $manipulator2]);
        $this->assertEquals(array_merge(Api::GLOBAL_API_PARAMS, ['foo', 'bar', 'baz']), $api->getApiParams());
    }

    public function testRun(): void
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock('\Intervention\Image\Origin', function ($mock) {
                $mock->shouldReceive('mediaType')->andReturn('image/png');
            }));

            $mock->shouldReceive('encodeByExtension')->with('png')->andReturn(\Mockery::mock(EncodedImageInterface::class, function ($mock) {
                $mock->shouldReceive('toString')->andReturn('encoded');
            }));
        });

        $manager = ImageManager::gd();

        $manipulator = \Mockery::mock(ManipulatorInterface::class, function ($mock) use ($image) {
            $mock->shouldReceive('setParams')->with([]);
            $mock->shouldReceive('run')->andReturn($image);
            $mock->shouldReceive('getApiParams')->andReturn(['p', 'q', 'fm', 's']);
        });

        $api = new Api($manager, [$manipulator]);

        $this->assertEquals('encoded', $api->run(
            (string) file_get_contents(dirname(__FILE__, 2).'/files/red-pixel.png'),
            []
        ));
    }
}
