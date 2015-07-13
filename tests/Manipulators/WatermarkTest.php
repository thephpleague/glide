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

        $this->manipulator->setParams(['mark' => 'image.jpg'])->getImage($image);
    }

    public function testGetDimension()
    {
        $image = Mockery::mock('Intervention\Image\Image');
        $image->shouldReceive('width')->andReturn(2000);
        $image->shouldReceive('height')->andReturn(1000);

        $this->assertSame(300.0, $this->manipulator->setParams(['w' => '300'])->getDimension($image, 'w'));
        $this->assertSame(300.0, $this->manipulator->setParams(['w' => 300])->getDimension($image, 'w'));
        $this->assertSame(1000.0, $this->manipulator->setParams(['w' => '50w'])->getDimension($image, 'w'));
        $this->assertSame(500.0, $this->manipulator->setParams(['w' => '50h'])->getDimension($image, 'w'));
        $this->assertSame(null, $this->manipulator->setParams(['w' => '101h'])->getDimension($image, 'w'));
        $this->assertSame(null, $this->manipulator->setParams(['w' => -1])->getDimension($image, 'w'));
        $this->assertSame(null, $this->manipulator->setParams(['w' => ''])->getDimension($image, 'w'));
    }

    public function testGetFit()
    {
        $this->assertSame('contain', $this->manipulator->setParams(['markfit' => 'contain'])->getFit());
        $this->assertSame('max', $this->manipulator->setParams(['markfit' => 'max'])->getFit());
        $this->assertSame('stretch', $this->manipulator->setParams(['markfit' => 'stretch'])->getFit());
        $this->assertSame('crop', $this->manipulator->setParams(['markfit' => 'crop'])->getFit());
        $this->assertSame('crop-top-left', $this->manipulator->setParams(['markfit' => 'crop-top-left'])->getFit());
        $this->assertSame('crop-top', $this->manipulator->setParams(['markfit' => 'crop-top'])->getFit());
        $this->assertSame('crop-top-right', $this->manipulator->setParams(['markfit' => 'crop-top-right'])->getFit());
        $this->assertSame('crop-left', $this->manipulator->setParams(['markfit' => 'crop-left'])->getFit());
        $this->assertSame('crop-center', $this->manipulator->setParams(['markfit' => 'crop-center'])->getFit());
        $this->assertSame('crop-right', $this->manipulator->setParams(['markfit' => 'crop-right'])->getFit());
        $this->assertSame('crop-bottom-left', $this->manipulator->setParams(['markfit' => 'crop-bottom-left'])->getFit());
        $this->assertSame('crop-bottom', $this->manipulator->setParams(['markfit' => 'crop-bottom'])->getFit());
        $this->assertSame('crop-bottom-right', $this->manipulator->setParams(['markfit' => 'crop-bottom-right'])->getFit());
        $this->assertSame(null, $this->manipulator->setParams(['markfit' => null])->getFit());
        $this->assertSame(null, $this->manipulator->setParams(['markfit' => 'invalid'])->getFit());
    }

    public function testGetPosition()
    {
        $this->assertSame('top-left', $this->manipulator->setParams(['markpos' => 'top-left'])->getPosition());
        $this->assertSame('top', $this->manipulator->setParams(['markpos' => 'top'])->getPosition());
        $this->assertSame('top-right', $this->manipulator->setParams(['markpos' => 'top-right'])->getPosition());
        $this->assertSame('left', $this->manipulator->setParams(['markpos' => 'left'])->getPosition());
        $this->assertSame('center', $this->manipulator->setParams(['markpos' => 'center'])->getPosition());
        $this->assertSame('right', $this->manipulator->setParams(['markpos' => 'right'])->getPosition());
        $this->assertSame('bottom-left', $this->manipulator->setParams(['markpos' => 'bottom-left'])->getPosition());
        $this->assertSame('bottom', $this->manipulator->setParams(['markpos' => 'bottom'])->getPosition());
        $this->assertSame('bottom-right', $this->manipulator->setParams(['markpos' => 'bottom-right'])->getPosition());
        $this->assertSame('bottom-right', $this->manipulator->setParams([])->getPosition());
        $this->assertSame('bottom-right', $this->manipulator->setParams(['markpos' => 'invalid'])->getPosition());
    }
}
