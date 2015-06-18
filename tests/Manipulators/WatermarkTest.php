<?php

namespace League\Glide\Manipulators;

use Mockery;

class WatermarkTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Watermark(
            Mockery::mock('League\Flysystem\FilesystemInterface')
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Watermark', $this->manipulator);
    }

    public function testGetImage()
    {
        $this->manipulator->getWatermarks()
            ->shouldReceive('has')
                ->with('image.jpg')
                ->andReturn(true)
                ->once()
            ->shouldReceive('read')
                ->with('image.jpg')
                ->andReturn('content')
                ->once();

        $driver = Mockery::mock('Intervention\Image\AbstractDriver');
        $driver->shouldReceive('init')
               ->with('content')
               ->andReturn(Mockery::mock('Intervention\Image\Image'))
               ->once();

        $image = Mockery::mock('Intervention\Image\Image');
        $image->shouldReceive('getDriver')
              ->andReturn($driver)
              ->once();

        $this->manipulator->getImage($image, ['mark' => 'image.jpg']);
    }

    public function testGetDimension()
    {
        $image = Mockery::mock('Intervention\Image\Image');
        $image->shouldReceive('width')->andReturn(2000);
        $image->shouldReceive('height')->andReturn(1000);

        $this->assertSame(300.0, $this->manipulator->getDimension($image, ['w' => '300'], 'w'));
        $this->assertSame(300.0, $this->manipulator->getDimension($image, ['w' => 300], 'w'));
        $this->assertSame(1000.0, $this->manipulator->getDimension($image, ['w' => '50w'], 'w'));
        $this->assertSame(500.0, $this->manipulator->getDimension($image, ['w' => '50h'], 'w'));
        $this->assertSame(null, $this->manipulator->getDimension($image, ['w' => '101h'], 'w'));
        $this->assertSame(null, $this->manipulator->getDimension($image, ['w' => -1], 'w'));
        $this->assertSame(null, $this->manipulator->getDimension($image, ['w' => ''], 'w'));
    }

    public function testGetFit()
    {
        $this->assertSame('contain', $this->manipulator->getFit(['markfit' => 'contain']));
        $this->assertSame('max', $this->manipulator->getFit(['markfit' => 'max']));
        $this->assertSame('stretch', $this->manipulator->getFit(['markfit' => 'stretch']));
        $this->assertSame('crop', $this->manipulator->getFit(['markfit' => 'crop']));
        $this->assertSame('crop-top-left', $this->manipulator->getFit(['markfit' => 'crop-top-left']));
        $this->assertSame('crop-top', $this->manipulator->getFit(['markfit' => 'crop-top']));
        $this->assertSame('crop-top-right', $this->manipulator->getFit(['markfit' => 'crop-top-right']));
        $this->assertSame('crop-left', $this->manipulator->getFit(['markfit' => 'crop-left']));
        $this->assertSame('crop-center', $this->manipulator->getFit(['markfit' => 'crop-center']));
        $this->assertSame('crop-right', $this->manipulator->getFit(['markfit' => 'crop-right']));
        $this->assertSame('crop-bottom-left', $this->manipulator->getFit(['markfit' => 'crop-bottom-left']));
        $this->assertSame('crop-bottom', $this->manipulator->getFit(['markfit' => 'crop-bottom']));
        $this->assertSame('crop-bottom-right', $this->manipulator->getFit(['markfit' => 'crop-bottom-right']));
        $this->assertSame(null, $this->manipulator->getFit(['markfit' => null]));
        $this->assertSame(null, $this->manipulator->getFit(['markfit' => 'invalid']));
    }

    public function testGetPosition()
    {
        $this->assertSame('top-left', $this->manipulator->getPosition(['markpos' => 'top-left']));
        $this->assertSame('top', $this->manipulator->getPosition(['markpos' => 'top']));
        $this->assertSame('top-right', $this->manipulator->getPosition(['markpos' => 'top-right']));
        $this->assertSame('left', $this->manipulator->getPosition(['markpos' => 'left']));
        $this->assertSame('center', $this->manipulator->getPosition(['markpos' => 'center']));
        $this->assertSame('right', $this->manipulator->getPosition(['markpos' => 'right']));
        $this->assertSame('bottom-left', $this->manipulator->getPosition(['markpos' => 'bottom-left']));
        $this->assertSame('bottom', $this->manipulator->getPosition(['markpos' => 'bottom']));
        $this->assertSame('bottom-right', $this->manipulator->getPosition(['markpos' => 'bottom-right']));
        $this->assertSame('bottom-right', $this->manipulator->getPosition([]));
        $this->assertSame('bottom-right', $this->manipulator->getPosition(['markpos' => 'invalid']));
    }
}
