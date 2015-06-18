<?php

namespace League\Glide\Manipulators;

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
        $this->assertInstanceOf('League\Glide\Manipulators\Contrast', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('contrast')->with('50')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run($image, ['con' => 50])
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->getContrast(['con' => '50']));
        $this->assertSame(50, $this->manipulator->getContrast(['con' => 50]));
        $this->assertSame(null, $this->manipulator->getContrast(['con' => null]));
        $this->assertSame(null, $this->manipulator->getContrast(['con' => '101']));
        $this->assertSame(null, $this->manipulator->getContrast(['con' => '-101']));
        $this->assertSame(null, $this->manipulator->getContrast(['con' => 'a']));
    }
}
