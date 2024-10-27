<?php

namespace League\Glide\Api;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\ManipulatorInterface;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private $api;

    public function setUp(): void
    {
        $this->api = new Api(ImageManager::gd(), []);
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf(Api::class, $this->api);
    }

    public function testSetImageManager()
    {
        $this->api->setImageManager(ImageManager::gd());
        $this->assertInstanceOf(ImageManager::class, $this->api->getImageManager());
    }

    public function testGetImageManager()
    {
        $this->assertInstanceOf(ImageManager::class, $this->api->getImageManager());
    }

    public function testSetManipulators()
    {
        $this->api->setManipulators([\Mockery::mock(ManipulatorInterface::class)]);
        $manipulators = $this->api->getManipulators();
        $this->assertInstanceOf(ManipulatorInterface::class, $manipulators[0]);
    }

    public function testSetInvalidManipulator()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not a valid manipulator.');

        $this->api->setManipulators([new \stdClass()]);
    }

    public function testGetManipulators()
    {
        $this->assertEquals([], $this->api->getManipulators());
    }

    public function testRun()
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
        });

        $api = new Api($manager, [$manipulator]);

        $this->assertEquals('encoded', $api->run(
            file_get_contents(dirname(__FILE__, 2).'/files/red-pixel.png'),
            []
        ));
    }
}
