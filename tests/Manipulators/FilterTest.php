<?php

namespace League\Glide\Manipulators;

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
                 ->shouldReceive('colorize')->with(38, 27, 12)->once()->andReturn($mock)
                 ->shouldReceive('invert')->once()->andReturn($mock);
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['filt' => 'greyscale'])->run($image)
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['filt' => 'sepia'])->run($image)
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['filt' => 'invert'])->run($image)
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams([])->run($image)
        );
    }

    public function testRunGreyscaleFilter()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('greyscale')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runGreyscaleFilter($image)
        );
    }

    public function testRunSepiaFilter()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('greyscale')->once()->andReturn($mock)
                 ->shouldReceive('brightness')->with(-10)->twice()->andReturn($mock)
                 ->shouldReceive('contrast')->with(10)->twice()->andReturn($mock)
                 ->shouldReceive('colorize')->with(38, 27, 12)->once()->andReturn($mock);
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runSepiaFilter($image)
        );
    }

    public function testRunInvertFilter()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('invert')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runInvertFilter($image)
        );
    }
}
