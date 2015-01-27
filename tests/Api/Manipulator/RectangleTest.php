<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
use Mockery;

class RectangleTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Rectangle();
        $this->image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('width')->andReturn(100);
            $mock->shouldReceive('height')->andReturn(100);
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Rectangle', $this->manipulator);
    }

    public function testRun()
    {
        $this->image->shouldReceive('crop')->with(100, 100, 0, 0)->once();

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', ['rect' => '100,100,0,0']), $this->image)
        );
    }

    public function testGetCoordinates()
    {
        $this->assertEquals([100, 100, 0, 0], $this->manipulator->getCoordinates($this->image, '100,100,0,0'));
        $this->assertEquals([101, 1, 1, 1], $this->manipulator->getCoordinates($this->image, '101,1,1,1'));
        $this->assertEquals([1, 101, 1, 1], $this->manipulator->getCoordinates($this->image, '1,101,1,1'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, null));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, '1,1,1,'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, '1,1,,1'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, '1,,1,1'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, ',1,1,1'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, '-1,1,1,1'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, '1,1,101,1'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, '1,1,1,101'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, 'a'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, ''));
    }

    public function testValidateCoordinates()
    {
        $this->assertEquals([100, 100, 0, 0], $this->manipulator->limitCoordinatesToImageBoundaries($this->image, [100, 100, 0, 0]));
        $this->assertEquals([90, 90, 10, 10], $this->manipulator->limitCoordinatesToImageBoundaries($this->image, [100, 100, 10, 10]));
    }
}
