<?php

namespace Glide\Manipulators;

use Glide\Request;
use Mockery;

class BlurTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Blur();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Manipulators\Blur', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('blur')->with('10')->once();
        });

        $this->manipulator->run(new Request('image.jpg', ['blur' => '10']), $image);
    }

    public function testGetBlur()
    {
        $this->assertEquals('50', $this->manipulator->getBlur('50'));
        $this->assertEquals(false, $this->manipulator->getBlur(null));
        $this->assertEquals(false, $this->manipulator->getBlur('50.0'));
        $this->assertEquals(false, $this->manipulator->getBlur('a'));
        $this->assertEquals(false, $this->manipulator->getBlur('-1'));
        $this->assertEquals(false, $this->manipulator->getBlur('101'));
    }
}
