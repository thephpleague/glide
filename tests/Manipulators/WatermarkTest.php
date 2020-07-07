<?php

namespace League\Glide\Manipulators;

use League\Glide\Filesystem\FilesystemException;
use Mockery;
use PHPUnit\Framework\TestCase;

class WatermarkTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Watermark(
            Mockery::mock('League\Flysystem\FilesystemInterface')
        );
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Watermark', $this->manipulator);
    }

    public function testSetWatermarks()
    {
        $this->manipulator->setWatermarks(Mockery::mock('League\Flysystem\FilesystemInterface'));
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->manipulator->getWatermarks());
    }

    public function testGetWatermarks()
    {
        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $this->manipulator->getWatermarks());
    }

    public function testSetWatermarksPathPrefix()
    {
        $this->manipulator->setWatermarksPathPrefix('watermarks/');
        $this->assertEquals('watermarks', $this->manipulator->getWatermarksPathPrefix());
    }

    public function testGetWatermarksPathPrefix()
    {
        $this->assertEquals('', $this->manipulator->getWatermarksPathPrefix());
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('insert')->once();
            $mock->shouldReceive('getDriver')->andReturn(Mockery::mock('Intervention\Image\AbstractDriver', function ($mock) {
                $mock->shouldReceive('init')->with('content')->andReturn(Mockery::mock('Intervention\Image\Image', function ($mock) {
                    $mock->shouldReceive('width')->andReturn(0)->once();
                    $mock->shouldReceive('resize')->once();
                }))->once();
            }))->once();
        });

        $this->manipulator->setWatermarks(Mockery::mock('League\Flysystem\FilesystemInterface', function ($watermarks) {
            $watermarks->shouldReceive('has')->with('image.jpg')->andReturn(true)->once();
            $watermarks->shouldReceive('read')->with('image.jpg')->andReturn('content')->once();
        }));

        $this->manipulator->setParams([
            'mark' => 'image.jpg',
            'markw' => '100',
            'markh' => '100',
            'markpad' => '10',
        ]);

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run($image)
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetImage()
    {
        $this->manipulator->getWatermarks()
            ->shouldReceive('has')
                ->with('watermarks/image.jpg')
                ->andReturn(true)
                ->once()
            ->shouldReceive('read')
                ->with('watermarks/image.jpg')
                ->andReturn('content')
                ->once();

        $this->manipulator->setWatermarksPathPrefix('watermarks');

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

    public function testGetImageWithUnreadableSource()
    {
        $this->expectException(FilesystemException::class);
        $this->expectExceptionMessage('Could not read the image `image.jpg`.');

        $this->manipulator->getWatermarks()
            ->shouldReceive('has')
                ->with('image.jpg')
                ->andReturn(true)
                ->once()
            ->shouldReceive('read')
                ->with('image.jpg')
                ->andReturn(false)
                ->once();

        $image = Mockery::mock('Intervention\Image\Image');

        $this->manipulator->setParams(['mark' => 'image.jpg'])->getImage($image);
    }

    public function testGetImageWithoutMarkParam()
    {
        $image = Mockery::mock('Intervention\Image\Image');

        $this->assertNull($this->manipulator->getImage($image));
    }

    public function testGetImageWithEmptyMarkParam()
    {
        $image = Mockery::mock('Intervention\Image\Image');

        $this->assertNull($this->manipulator->setParams(['mark' => ''])->getImage($image));
    }

    public function testGetImageWithoutWatermarksFilesystem()
    {
        $this->manipulator->setWatermarks(null);

        $image = Mockery::mock('Intervention\Image\Image');

        $this->assertNull($this->manipulator->setParams(['mark' => 'image.jpg'])->getImage($image));
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

    public function testGetDpr()
    {
        $this->assertSame(1.0, $this->manipulator->setParams(['dpr' => 'invalid'])->getDpr());
        $this->assertSame(1.0, $this->manipulator->setParams(['dpr' => '-1'])->getDpr());
        $this->assertSame(1.0, $this->manipulator->setParams(['dpr' => '9'])->getDpr());
        $this->assertSame(2.0, $this->manipulator->setParams(['dpr' => '2'])->getDpr());
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

    public function testGetAlpha()
    {
        $this->assertSame(100, $this->manipulator->setParams(['markalpha' => 'invalid'])->getAlpha());
        $this->assertSame(100, $this->manipulator->setParams(['markalpha' => 255])->getAlpha());
        $this->assertSame(100, $this->manipulator->setParams(['markalpha' => -1])->getAlpha());
        $this->assertSame(65, $this->manipulator->setParams(['markalpha' => '65'])->getAlpha());
        $this->assertSame(65, $this->manipulator->setParams(['markalpha' => 65])->getAlpha());
    }
}
