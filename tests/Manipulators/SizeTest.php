<?php

namespace League\Glide\Manipulators;

use League\Glide\Requests\RequestFactory;
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
            $this->manipulator->run(RequestFactory::create(['image.jpg', ['w' => '100']]), $image)
        );
    }

    public function testGetWidth()
    {
        $this->assertSame(100.0, $this->manipulator->getWidth('100'));
        $this->assertSame(100.1, $this->manipulator->getWidth(100.1));
        $this->assertSame(false, $this->manipulator->getWidth(null));
        $this->assertSame(false, $this->manipulator->getWidth('a'));
        $this->assertSame(false, $this->manipulator->getWidth('-100'));
    }

    public function testGetHeight()
    {
        $this->assertSame(100.0, $this->manipulator->getHeight('100'));
        $this->assertSame(100.1, $this->manipulator->getHeight(100.1));
        $this->assertSame(false, $this->manipulator->getHeight(null));
        $this->assertSame(false, $this->manipulator->getHeight('a'));
        $this->assertSame(false, $this->manipulator->getHeight('-100'));
    }

    public function testGetFit()
    {
        $this->assertSame('contain', $this->manipulator->getFit('contain'));
        $this->assertSame('max', $this->manipulator->getFit('max'));
        $this->assertSame('stretch', $this->manipulator->getFit('stretch'));
        $this->assertSame('crop', $this->manipulator->getFit('crop'));
        $this->assertSame('contain', $this->manipulator->getFit('invalid'));
    }

    public function testGetCrop()
    {
        $this->assertSame('top-left', $this->manipulator->getCrop('top-left'));
        $this->assertSame('top', $this->manipulator->getCrop('top'));
        $this->assertSame('top-right', $this->manipulator->getCrop('top-right'));
        $this->assertSame('left', $this->manipulator->getCrop('left'));
        $this->assertSame('center', $this->manipulator->getCrop('center'));
        $this->assertSame('right', $this->manipulator->getCrop('right'));
        $this->assertSame('bottom-left', $this->manipulator->getCrop('bottom-left'));
        $this->assertSame('bottom', $this->manipulator->getCrop('bottom'));
        $this->assertSame('bottom-right', $this->manipulator->getCrop('bottom-right'));
        $this->assertSame('center', $this->manipulator->getCrop(null));
        $this->assertSame('center', $this->manipulator->getCrop('invalid'));
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
