<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Origin;
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

    public function testRun(): void
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock(Origin::class, function ($mock) {
                $mock->shouldReceive('mediaType')->andReturn('image/png');
            }));

            $mock->shouldReceive('driver')->andReturn(\Mockery::mock(DriverInterface::class, function ($mock) {
                $mock->shouldReceive('supports');
            }));

            $mock->shouldReceive('encode')->andReturn(\Mockery::mock(EncodedImageInterface::class, function ($mock) {
                $mock->shouldReceive('toString')->andReturn('encoded');
            }));
        });

        $manager = ImageManager::gd();

        $manipulator = \Mockery::mock(ManipulatorInterface::class, function ($mock) use ($image) {
            $mock->shouldReceive('setParams')->with([]);
            $mock->shouldReceive('run')->andReturn($image);
        });

        $api = new Api($manager, [$manipulator]);

        $this->assertEquals('encoded', $api->run(
            (string) file_get_contents(dirname(__FILE__, 2).'/files/red-pixel.png'),
            []
        ));
    }
}
