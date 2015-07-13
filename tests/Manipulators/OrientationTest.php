<?php

namespace League\Glide\Manipulators;

use Mockery;

class OrientationTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Orientation();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Orientation', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('orientate')->andReturn($mock)->once();
            $mock->shouldReceive('rotate')->andReturn($mock)->with('90')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['or' => 'auto'])->run($image)
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['or' => '90'])->run($image)
        );
    }

    public function testGetOrientation()
    {
        $this->assertSame('auto', $this->manipulator->setParams(['or' => 'auto'])->getOrientation());
        $this->assertSame('0', $this->manipulator->setParams(['or' => '0'])->getOrientation());
        $this->assertSame('90', $this->manipulator->setParams(['or' => '90'])->getOrientation());
        $this->assertSame('180', $this->manipulator->setParams(['or' => '180'])->getOrientation());
        $this->assertSame('270', $this->manipulator->setParams(['or' => '270'])->getOrientation());
        $this->assertSame('auto', $this->manipulator->setParams(['or' => null])->getOrientation());
        $this->assertSame('auto', $this->manipulator->setParams(['or' => '1'])->getOrientation());
        $this->assertSame('auto', $this->manipulator->setParams(['or' => '45'])->getOrientation());
    }
}
