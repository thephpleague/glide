<?php

namespace League\Glide\Manipulators;

use Mockery;

class CropTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Crop();
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
        $this->assertInstanceOf('League\Glide\Manipulators\Crop', $this->manipulator);
    }

    public function testRun()
    {
        $this->image->shouldReceive('crop')->with(100, 100, 0, 0)->once();

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run($this->image, ['crop' => '100,100,0,0'])
        );
    }

    public function testGetCoordinates()
    {
        $this->assertSame([100, 100, 0, 0], $this->manipulator->getCoordinates($this->image, ['crop' => '100,100,0,0']));
        $this->assertSame([101, 1, 1, 1], $this->manipulator->getCoordinates($this->image, ['crop' => '101,1,1,1']));
        $this->assertSame([1, 101, 1, 1], $this->manipulator->getCoordinates($this->image, ['crop' => '1,101,1,1']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => null]));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => '1,1,1,']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => '1,1,,1']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => '1,,1,1']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => ',1,1,1']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => '-1,1,1,1']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => '1,1,101,1']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => '1,1,1,101']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => 'a']));
        $this->assertSame(null, $this->manipulator->getCoordinates($this->image, ['crop' => '']));
    }

    public function testValidateCoordinates()
    {
        $this->assertSame([100, 100, 0, 0], $this->manipulator->limitToImageBoundaries($this->image, [100, 100, 0, 0]));
        $this->assertSame([90, 90, 10, 10], $this->manipulator->limitToImageBoundaries($this->image, [100, 100, 10, 10]));
    }
}
