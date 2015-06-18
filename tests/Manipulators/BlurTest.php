<?php

namespace League\Glide\Manipulators;

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
        $this->assertInstanceOf('League\Glide\Manipulators\Blur', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('blur')->with('10')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run($image, ['blur' => 10])
        );
    }

    public function testGetBlur()
    {
        $this->assertSame(50, $this->manipulator->getBlur(['blur' => '50']));
        $this->assertSame(50, $this->manipulator->getBlur(['blur' => 50]));
        $this->assertSame(null, $this->manipulator->getBlur(['blur' => null]));
        $this->assertSame(null, $this->manipulator->getBlur(['blur' => 'a']));
        $this->assertSame(null, $this->manipulator->getBlur(['blur' => '-1']));
        $this->assertSame(null, $this->manipulator->getBlur(['blur' => '101']));
    }
}
