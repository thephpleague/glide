<?php

namespace League\Glide\Manipulators;

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

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run($image, ['bri' => 50])
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->getBrightness(['bri' => '50']));
        $this->assertSame(50, $this->manipulator->getBrightness(['bri' => 50]));
        $this->assertSame(null, $this->manipulator->getBrightness(['bri' => null]));
        $this->assertSame(null, $this->manipulator->getBrightness(['bri' => '101']));
        $this->assertSame(null, $this->manipulator->getBrightness(['bri' => '-101']));
        $this->assertSame(null, $this->manipulator->getBrightness(['bri' => 'a']));
    }
}
