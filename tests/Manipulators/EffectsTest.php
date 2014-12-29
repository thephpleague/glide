<?php

namespace Glide\Manipulators;

use Glide\Request;
use Mockery;

class EffectsTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Effects();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Manipulators\Effects', $this->manipulator);
    }

    public function testValidate()
    {
        $request = new Request('image.jpg');
        $image = Mockery::mock('Intervention\Image\Image');

        $this->assertEquals([], $this->manipulator->validate($request, $image));
    }

    public function testValidateFilter()
    {
        $this->assertEquals([], $this->manipulator->validateFilter(null));
        $this->assertEquals([], $this->manipulator->validateFilter('greyscale'));
        $this->assertEquals([], $this->manipulator->validateFilter('sepia'));
        $this->assertEquals(['filt' => 'Filter only accepts `greyscale` or `sepia`.'], $this->manipulator->validateFilter('invalid'));
    }

    public function testValidateBlur()
    {
        $this->assertEquals([], $this->manipulator->validateBlur(null));
        $this->assertEquals([], $this->manipulator->validateBlur('50'));
        $this->assertEquals(['blur' => 'Blur must be a valid number.'], $this->manipulator->validateBlur('50.0'));
        $this->assertEquals(['blur' => 'Blur must be a valid number.'], $this->manipulator->validateBlur('a'));
        $this->assertEquals(['blur' => 'Blur must be a valid number.'], $this->manipulator->validateBlur('-1'));
        $this->assertEquals(['blur' => 'Blur must be between `0` and `100`.'], $this->manipulator->validateBlur('101'));
    }

    public function testValidatePixelate()
    {
        $this->assertEquals([], $this->manipulator->validatePixelate(null));
        $this->assertEquals([], $this->manipulator->validatePixelate('50'));
        $this->assertEquals(['pixel' => 'Pixelate must be a valid number.'], $this->manipulator->validatePixelate('50.0'));
        $this->assertEquals(['pixel' => 'Pixelate must be a valid number.'], $this->manipulator->validatePixelate('a'));
        $this->assertEquals(['pixel' => 'Pixelate must be a valid number.'], $this->manipulator->validatePixelate('-1'));
        $this->assertEquals(['pixel' => 'Pixelate must be between `0` and `1000`.'], $this->manipulator->validatePixelate('1001'));
    }

    public function testRun()
    {
        $request = new Request('image.jpg', [
            'filt' => 'greyscale',
            'blur' => '10',
            'pixel' => '10',
        ]);

        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('greyscale')->once()->andReturn($mock)
                 ->shouldReceive('blur')->with(10)->once()->andReturn($mock)
                 ->shouldReceive('pixelate')->with(10)->once()->andReturn($mock);
        });

        $this->manipulator->run($request, $image);
    }

    public function testRunFilter()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('greyscale')->twice()->andReturn($mock)
                 ->shouldReceive('brightness')->with(-10)->twice()->andReturn($mock)
                 ->shouldReceive('contrast')->with(10)->twice()->andReturn($mock)
                 ->shouldReceive('colorize')->with(38, 27, 12)->once()->andReturn($mock);
        });

        $this->manipulator->runFilter($image, 'greyscale');
        $this->manipulator->runFilter($image, 'sepia');
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

    public function testRunBlur()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('blur')->with(10)->once();
        });

        $this->manipulator->runBlur($image, 10);
    }

    public function testRunPixelate()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('pixelate')->with(10)->once();
        });

        $this->manipulator->runPixelate($image, 10);
    }
}
