<?php

namespace League\Glide\Manipulators;

use League\Glide\Requests\RequestFactory;
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
        $this->assertInstanceOf('League\Glide\Manipulators\Rectangle', $this->manipulator);
    }

    public function testRun()
    {
        $this->image->shouldReceive('crop')->with(100, 100, 0, 0)->once();

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create(['image.jpg', ['rect' => '100,100,0,0']]), $this->image)
        );
    }

    public function testGetCoordinates()
    {
        $this->assertSame([100, 100, 0, 0], $this->manipulator->getCoordinates($this->image, '100,100,0,0'));
        $this->assertSame([101, 1, 1, 1], $this->manipulator->getCoordinates($this->image, '101,1,1,1'));
        $this->assertSame([1, 101, 1, 1], $this->manipulator->getCoordinates($this->image, '1,101,1,1'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, null));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, '1,1,1,'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, '1,1,,1'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, '1,,1,1'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, ',1,1,1'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, '-1,1,1,1'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, '1,1,101,1'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, '1,1,1,101'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, 'a'));
        $this->assertSame(false, $this->manipulator->getCoordinates($this->image, ''));
    }

    public function testValidateCoordinates()
    {
        $this->assertSame([100, 100, 0, 0], $this->manipulator->limitCoordinatesToImageBoundaries($this->image, [100, 100, 0, 0]));
        $this->assertSame([90, 90, 10, 10], $this->manipulator->limitCoordinatesToImageBoundaries($this->image, [100, 100, 10, 10]));
    }
}
