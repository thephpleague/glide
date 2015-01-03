<?php

namespace League\Glide\Manipulators;

use League\Glide\Request;
use Mockery;

class BrightnessTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Brightness();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Brightness', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('brightness')->with('50')->once();
        });

        $this->manipulator->run(new Request('image.jpg', ['bri' => '50']), $image);
    }

    public function testGetPixelate()
    {
        $this->assertEquals('50', $this->manipulator->getBrightness('50'));
        $this->assertEquals(false, $this->manipulator->getBrightness(null));
        $this->assertEquals(false, $this->manipulator->getBrightness('101'));
        $this->assertEquals(false, $this->manipulator->getBrightness('-101'));
        $this->assertEquals(false, $this->manipulator->getBrightness('a'));
    }
}
