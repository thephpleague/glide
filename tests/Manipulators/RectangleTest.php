<?php

namespace League\Glide\Manipulators;

use League\Glide\Request;
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
        $this->image->shouldReceive('crop')->with(100, 100, 100, 100)->once();

        $this->manipulator->run(new Request('image.jpg', ['rect' => '100,100,100,100']), $this->image);
    }

    public function testGetCoordinates()
    {
        $this->assertEquals(['100', '100', '100', '100'], $this->manipulator->getCoordinates($this->image, '100,100,100,100'));
        $this->assertEquals(false, $this->manipulator->getCoordinates($this->image, null));
    }

    public function testValidateCoordinates()
    {
        $this->assertEquals(true, $this->manipulator->validateCoordinates($this->image, ['100', '100', '100', '100']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['1', '1', '1', '']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['1', '1', '', '1']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['1', '', '1', '1']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['', '1', '1', '1']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['-1', '1', '1', '1']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['101', '1', '1', '1']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['1', '101', '1', '1']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['1', '1', '101', '1']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['1', '1', '1', '101']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, ['a']));
        $this->assertEquals(false, $this->manipulator->validateCoordinates($this->image, []));
    }
}
