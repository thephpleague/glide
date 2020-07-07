<?php

namespace League\Glide\Api;

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private $output;

    public function setUp(): void
    {
        $this->api = new Api(Mockery::mock('Intervention\Image\ImageManager'), []);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Api\Api', $this->api);
    }

    public function testSetImageManager()
    {
        $this->api->setImageManager(Mockery::mock('Intervention\Image\ImageManager'));
        $this->assertInstanceOf('Intervention\Image\ImageManager', $this->api->getImageManager());
    }

    public function testGetImageManager()
    {
        $this->assertInstanceOf('Intervention\Image\ImageManager', $this->api->getImageManager());
    }

    public function testSetManipulators()
    {
        $this->api->setManipulators([Mockery::mock('League\Glide\Manipulators\ManipulatorInterface')]);
        $manipulators = $this->api->getManipulators();
        $this->assertInstanceOf('League\Glide\Manipulators\ManipulatorInterface', $manipulators[0]);
    }

    public function testSetInvalidManipulator()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Not a valid manipulator.');

        $this->api->setManipulators([new \StdClass()]);
    }

    public function testGetManipulators()
    {
        $this->assertEquals([], $this->api->getManipulators());
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('getEncoded')->andReturn('encoded');
        });

        $manager = Mockery::mock('Intervention\Image\ImageManager', function ($mock) use ($image) {
            $mock->shouldReceive('make')->andReturn($image);
        });

        $manipulator = Mockery::mock('League\Glide\Manipulators\ManipulatorInterface', function ($mock) use ($image) {
            $mock->shouldReceive('setParams')->with([]);
            $mock->shouldReceive('run')->andReturn($image);
        });

        $api = new Api($manager, [$manipulator]);

        $this->assertEquals('encoded', $api->run('source', []));
    }
}
