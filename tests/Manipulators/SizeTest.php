<?php

namespace League\Glide\Manipulators;

use Mockery;

class SizeTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;
    private $callback;

    public function setUp()
    {
        $this->manipulator = new Size();
        $this->callback = Mockery::on(function () {
            return true;
        });
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Size', $this->manipulator);
    }

    public function testSetMaxImageSize()
    {
        $this->manipulator->setMaxImageSize(500 * 500);
        $this->assertSame(500 * 500, $this->manipulator->getMaxImageSize());
    }

    public function testGetMaxImageSize()
    {
        $this->assertNull($this->manipulator->getMaxImageSize());
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('width')->andReturn('200')->twice();
            $mock->shouldReceive('height')->andReturn('200')->once();
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run($image, ['w' => 100])
        );
    }

    public function testGetWidth()
    {
        $this->assertSame(100.0, $this->manipulator->getWidth(['w' => '100']));
        $this->assertSame(100.1, $this->manipulator->getWidth(['w' => 100.1]));
        $this->assertSame(null, $this->manipulator->getWidth(['w' => null]));
        $this->assertSame(null, $this->manipulator->getWidth(['w' => 'a']));
        $this->assertSame(null, $this->manipulator->getWidth(['w' => '-100']));
    }

    public function testGetHeight()
    {
        $this->assertSame(100.0, $this->manipulator->getHeight(['h' => '100']));
        $this->assertSame(100.1, $this->manipulator->getHeight(['h' => 100.1]));
        $this->assertSame(null, $this->manipulator->getHeight(['h' => null]));
        $this->assertSame(null, $this->manipulator->getHeight(['h' => 'a']));
        $this->assertSame(null, $this->manipulator->getHeight(['h' => '-100']));
    }

    public function testGetFit()
    {
        $this->assertSame('contain', $this->manipulator->getFit(['fit' => 'contain']));
        $this->assertSame('max', $this->manipulator->getFit(['fit' => 'max']));
        $this->assertSame('stretch', $this->manipulator->getFit(['fit' => 'stretch']));
        $this->assertSame('crop', $this->manipulator->getFit(['fit' => 'crop']));
        $this->assertSame('contain', $this->manipulator->getFit(['fit' => 'invalid']));
    }

    public function testGetCrop()
    {
        $this->assertSame('center', $this->manipulator->getCrop(['fit' => 'crop']));
        $this->assertSame('top-left', $this->manipulator->getCrop(['fit' => 'crop-top-left']));
        $this->assertSame('top', $this->manipulator->getCrop(['fit' => 'crop-top']));
        $this->assertSame('top-right', $this->manipulator->getCrop(['fit' => 'crop-top-right']));
        $this->assertSame('left', $this->manipulator->getCrop(['fit' => 'crop-left']));
        $this->assertSame('center', $this->manipulator->getCrop(['fit' => 'crop-center']));
        $this->assertSame('right', $this->manipulator->getCrop(['fit' => 'crop-right']));
        $this->assertSame('bottom-left', $this->manipulator->getCrop(['fit' => 'crop-bottom-left']));
        $this->assertSame('bottom', $this->manipulator->getCrop(['fit' => 'crop-bottom']));
        $this->assertSame('bottom-right', $this->manipulator->getCrop(['fit' => 'crop-bottom-right']));
        $this->assertSame('center', $this->manipulator->getCrop(['fit' => null]));
        $this->assertSame('center', $this->manipulator->getCrop(['fit' => 'invalid']));
    }

    public function testResolveMissingDimensions()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('width')->andReturn(400);
            $mock->shouldReceive('height')->andReturn(200);
        });

        $this->assertSame([400.0, 200.0], $this->manipulator->resolveMissingDimensions($image, false, false));
        $this->assertSame([100.0, 50.0], $this->manipulator->resolveMissingDimensions($image, 100, false));
        $this->assertSame([200.0, 100.0], $this->manipulator->resolveMissingDimensions($image, false, 100));
    }

    public function testLimitImageSize()
    {
        $this->assertSame([1000.0, 1000.0], $this->manipulator->limitImageSize(1000, 1000));
        $this->manipulator->setMaxImageSize(500 * 500);
        $this->assertSame([500.0, 500.0], $this->manipulator->limitImageSize(500, 500));
        $this->assertSame([500.0, 500.0], $this->manipulator->limitImageSize(1000, 1000));
    }

    public function testRunResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->andReturn($mock)->twice();
            $mock->shouldReceive('resize')->with('100', '100')->andReturn($mock)->once();
            $mock->shouldReceive('fit')->with('100', '100', $this->callback, 'center')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runResize($image, 'contain', '100', '100')
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runResize($image, 'max', '100', '100')
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runResize($image, 'stretch', '100', '100')
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runResize($image, 'crop', '100', '100', 'center')
        );
    }

    public function testRunContainResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runContainResize($image, '100', '100')
        );
    }

    public function testRunMaxResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100', $this->callback)->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runMaxResize($image, '100', '100')
        );
    }

    public function testRunStretchResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('resize')->with('100', '100')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runStretchResize($image, '100', '100')
        );
    }

    public function testRunCropResize()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('fit')->with('100', '100', $this->callback, 'center')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->runCropResize($image, '100', '100', 'center')
        );
    }
}
