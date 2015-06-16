<?php

namespace League\Glide\Manipulators;

use League\Glide\Requests\RequestFactory;
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
        $this->assertInstanceOf('League\Glide\Manipulators\Pixelate', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('pixelate')->with('10')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create(['image.jpg', ['pixel' => '10']]), $image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->getPixelate('50'));
        $this->assertSame(50, $this->manipulator->getPixelate(50.50));
        $this->assertSame(false, $this->manipulator->getPixelate(null));
        $this->assertSame(false, $this->manipulator->getPixelate('a'));
        $this->assertSame(false, $this->manipulator->getPixelate('-1'));
        $this->assertSame(false, $this->manipulator->getPixelate('1001'));
    }
}
