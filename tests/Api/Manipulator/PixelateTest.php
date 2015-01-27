<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
use Mockery;

class PixelateTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Pixelate();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Pixelate', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('pixelate')->with('10')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', ['pixel' => '10']), $image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertEquals('50', $this->manipulator->getPixelate('50'));
        $this->assertEquals(false, $this->manipulator->getPixelate(null));
        $this->assertEquals(false, $this->manipulator->getPixelate('50.0'));
        $this->assertEquals(false, $this->manipulator->getPixelate('a'));
        $this->assertEquals(false, $this->manipulator->getPixelate('-1'));
        $this->assertEquals(false, $this->manipulator->getPixelate('1001'));
    }
}
