<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
use Mockery;

class SharpenTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Sharpen();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Sharpen', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('sharpen')->with('10')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', ['sharp' => '10']), $image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertEquals('10', $this->manipulator->getSharpen('10'));
        $this->assertEquals(false, $this->manipulator->getSharpen(null));
        $this->assertEquals(false, $this->manipulator->getSharpen('50.0'));
        $this->assertEquals(false, $this->manipulator->getSharpen('a'));
        $this->assertEquals(false, $this->manipulator->getSharpen('-1'));
        $this->assertEquals(false, $this->manipulator->getSharpen('101'));
    }
}
