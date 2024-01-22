<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Filter();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Filter', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('greyscale')->twice()->andReturn($mock)
                 ->shouldReceive('brightness')->with(-10)->twice()->andReturn($mock)
                 ->shouldReceive('contrast')->with(10)->twice()->andReturn($mock)
                 ->shouldReceive('colorize')->with(38, 27, 12)->once()->andReturn($mock);
        });

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->setParams(['filt' => 'greyscale'])->run($image)
        );

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->setParams(['filt' => 'sepia'])->run($image)
        );

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->setParams([])->run($image)
        );
    }

    public function testRunGreyscaleFilter()
    {
        $image = Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('greyscale')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->runGreyscaleFilter($image)
        );
    }

    public function testRunSepiaFilter()
    {
        $image = Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('greyscale')->once()->andReturn($mock)
                 ->shouldReceive('brightness')->with(-10)->twice()->andReturn($mock)
                 ->shouldReceive('contrast')->with(10)->twice()->andReturn($mock)
                 ->shouldReceive('colorize')->with(38, 27, 12)->once()->andReturn($mock);
        });

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->runSepiaFilter($image)
        );
    }
}
