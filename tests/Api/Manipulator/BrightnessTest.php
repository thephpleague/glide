<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
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
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Brightness', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('brightness')->with('50')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', ['bri' => '50']), $image)
        );
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
