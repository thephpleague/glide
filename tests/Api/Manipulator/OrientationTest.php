<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
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
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Orientation', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('orientate')->andReturn($mock)->once();
            $mock->shouldReceive('rotate')->andReturn($mock)->with('90')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', ['or' => 'auto']), $image)
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', ['or' => '90']), $image)
        );
    }

    public function testGetOrientation()
    {
        $this->assertEquals('auto', $this->manipulator->getOrientation('auto'));
        $this->assertEquals('0', $this->manipulator->getOrientation('0'));
        $this->assertEquals('90', $this->manipulator->getOrientation('90'));
        $this->assertEquals('180', $this->manipulator->getOrientation('180'));
        $this->assertEquals('270', $this->manipulator->getOrientation('270'));
        $this->assertEquals('auto', $this->manipulator->getOrientation(null));
        $this->assertEquals('auto', $this->manipulator->getOrientation('1'));
        $this->assertEquals('auto', $this->manipulator->getOrientation('45'));
    }
}
