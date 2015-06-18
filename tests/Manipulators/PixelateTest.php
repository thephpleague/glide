<?php

namespace League\Glide\Manipulators;

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
            $this->manipulator->run($image, ['pixel' => '10'])
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->getPixelate(['pixel' => '50']));
        $this->assertSame(50, $this->manipulator->getPixelate(['pixel' => 50.50]));
        $this->assertSame(null, $this->manipulator->getPixelate(['pixel' => null]));
        $this->assertSame(null, $this->manipulator->getPixelate(['pixel' => 'a']));
        $this->assertSame(null, $this->manipulator->getPixelate(['pixel' => '-1']));
        $this->assertSame(null, $this->manipulator->getPixelate(['pixel' => '1001']));
    }
}
