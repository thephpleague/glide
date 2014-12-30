<?php

namespace Glide\Manipulators;

use Glide\Request;
use Mockery;

class ContrastTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Contrast();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Manipulators\Contrast', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('contrast')->with('50')->once();
        });

        $this->manipulator->run(new Request('image.jpg', ['con' => '50']), $image);
    }

    public function testGetPixelate()
    {
        $this->assertEquals('50', $this->manipulator->getContrast('50'));
        $this->assertEquals(false, $this->manipulator->getContrast(null));
        $this->assertEquals(false, $this->manipulator->getContrast('101'));
        $this->assertEquals(false, $this->manipulator->getContrast('-101'));
        $this->assertEquals(false, $this->manipulator->getContrast('a'));
    }
}
