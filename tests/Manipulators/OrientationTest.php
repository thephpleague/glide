<?php

namespace League\Glide\Manipulators;

use League\Glide\Request;
use Mockery;

class OrientationTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Orientation();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Orientation', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('orientate')->once();
            $mock->shouldReceive('rotate')->with('90')->once();
        });

        $this->manipulator->run(new Request('image.jpg', ['or' => 'auto']), $image);
        $this->manipulator->run(new Request('image.jpg', ['or' => '90']), $image);
    }

    public function testGetOrientation()
    {
        $this->assertEquals('auto', $this->manipulator->getOrientation('auto'));
        $this->assertEquals('0', $this->manipulator->getOrientation('0'));
        $this->assertEquals('90', $this->manipulator->getOrientation('90'));
        $this->assertEquals('180', $this->manipulator->getOrientation('180'));
        $this->assertEquals('270', $this->manipulator->getOrientation('270'));
        $this->assertEquals('auto', $this->manipulator->getOrientation(null));
        $this->assertEquals('auto', $this->manipulator->getOrientation('1'));
        $this->assertEquals('auto', $this->manipulator->getOrientation('45'));
    }
}
