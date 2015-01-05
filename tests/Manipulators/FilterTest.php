<?php

namespace League\Glide\Manipulators;

use League\Glide\ImageRequest;
use Mockery;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Filter();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Filter', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('greyscale')->twice()->andReturn($mock)
                 ->shouldReceive('brightness')->with(-10)->twice()->andReturn($mock)
                 ->shouldReceive('contrast')->with(10)->twice()->andReturn($mock)
                 ->shouldReceive('colorize')->with(38, 27, 12)->once()->andReturn($mock);
        });

        $this->manipulator->run(new ImageRequest('image.jpg', ['filt' => 'greyscale']), $image);
        $this->manipulator->run(new ImageRequest('image.jpg', ['filt' => 'sepia']), $image);
    }

    public function testRunGreyscaleFilter()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('greyscale')->once();
        });

        $this->manipulator->runGreyscaleFilter($image);
    }

    public function testRunSepiaFilter()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('greyscale')->once()->andReturn($mock)
                 ->shouldReceive('brightness')->with(-10)->twice()->andReturn($mock)
                 ->shouldReceive('contrast')->with(10)->twice()->andReturn($mock)
                 ->shouldReceive('colorize')->with(38, 27, 12)->once()->andReturn($mock);
        });

        $this->manipulator->runSepiaFilter($image);
    }
}
