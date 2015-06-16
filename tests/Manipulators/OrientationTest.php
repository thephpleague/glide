<?php

namespace League\Glide\Manipulators;

use League\Glide\Requests\RequestFactory;
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
            $this->manipulator->run(RequestFactory::create(['image.jpg', ['or' => 'auto']]), $image)
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create(['image.jpg', ['or' => '90']]), $image)
        );
    }

    public function testGetOrientation()
    {
        $this->assertSame('auto', $this->manipulator->getOrientation('auto'));
        $this->assertSame('0', $this->manipulator->getOrientation('0'));
        $this->assertSame('90', $this->manipulator->getOrientation('90'));
        $this->assertSame('180', $this->manipulator->getOrientation('180'));
        $this->assertSame('270', $this->manipulator->getOrientation('270'));
        $this->assertSame('auto', $this->manipulator->getOrientation(null));
        $this->assertSame('auto', $this->manipulator->getOrientation('1'));
        $this->assertSame('auto', $this->manipulator->getOrientation('45'));
    }
}
