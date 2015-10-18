<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
use Mockery;

class CircleTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Circle();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Circle', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('mask')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', [
                'w'   => 300,
                'h'   => 300,
                'fit' => 'crop',
                'fm'  => 'png',
                'circle',
            ]), $image)
        );
    }

    public function testGetWidth()
    {
        $this->assertEquals('100', $this->manipulator->getWidth('100'));
        $this->assertEquals(false, $this->manipulator->getWidth(null));
        $this->assertEquals(false, $this->manipulator->getWidth('a'));
        $this->assertEquals(false, $this->manipulator->getWidth('100.1'));
        $this->assertEquals(false, $this->manipulator->getWidth('-100'));
    }

    public function testGetHeight()
    {
        $this->assertEquals('100', $this->manipulator->getHeight('100'));
        $this->assertEquals(false, $this->manipulator->getHeight(null));
        $this->assertEquals(false, $this->manipulator->getHeight('a'));
        $this->assertEquals(false, $this->manipulator->getHeight('100.1'));
        $this->assertEquals(false, $this->manipulator->getHeight('-100'));
    }

    public function testResolveMissingDimensions()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('width')->andReturn(400);
            $mock->shouldReceive('height')->andReturn(200);
        });

        $this->assertEquals([400, 200], $this->manipulator->resolveMissingDimensions($image, false, false));
        $this->assertEquals([100, 50], $this->manipulator->resolveMissingDimensions($image, 100, false));
        $this->assertEquals([200, 100], $this->manipulator->resolveMissingDimensions($image, false, 100));
    }

    public function testLimitImageSize()
    {
        $this->assertEquals([1000, 1000], $this->manipulator->limitImageSize(1000, 1000));
        $this->manipulator->setMaxImageSize(500 * 500);
        $this->assertEquals([500, 500], $this->manipulator->limitImageSize(500, 500));
        $this->assertEquals([500, 500], $this->manipulator->limitImageSize(1000, 1000));
    }
}
