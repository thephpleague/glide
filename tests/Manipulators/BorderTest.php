<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Interfaces\ImageInterface;
use PHPUnit\Framework\TestCase;

class BorderTest extends TestCase
{
    private $manipulator;

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Border', new Border());
    }

    public function testGetBorder()
    {
        $image = \Mockery::mock(ImageInterface::class);

        $border = new Border();

        $this->assertNull($border->getBorder($image));

        $this->assertSame(
            [10.0, 'rgba(0, 0, 0, 1)', 'overlay'],
            $border->setParams(['border' => '10,black'])->getBorder($image)
        );
    }

    public function testGetInvalidBorder()
    {
        $image = \Mockery::mock(ImageInterface::class);

        $border = new Border();

        $this->assertNull(
            $border->setParams(['border' => '0,black'])->getBorder($image)
        );
    }

    public function testGetWidth()
    {
        $image = \Mockery::mock(ImageInterface::class);

        $border = new Border();

        $this->assertSame(100.0, $border->getWidth($image, 1, '100'));
    }

    public function testGetColor()
    {
        $border = new Border();

        $this->assertSame('rgba(0, 0, 0, 1)', $border->getColor('black'));
    }

    public function testGetMethod()
    {
        $border = new Border();

        $this->assertSame('expand', $border->getMethod('expand'));
        $this->assertSame('shrink', $border->getMethod('shrink'));
        $this->assertSame('overlay', $border->getMethod('overlay'));
        $this->assertSame('overlay', $border->getMethod('invalid'));
    }

    public function testGetDpr()
    {
        $border = new Border();

        $this->assertSame(1.0, $border->setParams(['dpr' => 'invalid'])->getDpr());
        $this->assertSame(1.0, $border->setParams(['dpr' => '-1'])->getDpr());
        $this->assertSame(1.0, $border->setParams(['dpr' => '9'])->getDpr());
        $this->assertSame(2.0, $border->setParams(['dpr' => '2'])->getDpr());
    }

    public function testRunWithNoBorder()
    {
        $image = \Mockery::mock(ImageInterface::class);

        $border = new Border();

        $this->assertInstanceOf(ImageInterface::class, $border->run($image));
    }

    public function testRunOverlay()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('width')->andReturn(100)->once();
            $mock->shouldReceive('height')->andReturn(100)->once();
            $mock->shouldReceive('drawRectangle')->with(5, 5, \Mockery::on(function ($closure) {
                $mock2 = \Mockery::mock(RectangleFactory::class, function ($mock2) {
                    $mock2->shouldReceive('size')->once();
                    $mock2->shouldReceive('border')->once();
                });

                $closure($mock2);

                return true;
            }))->andReturn($mock)->once();
        });

        $border = new Border();
        $border->setParams(['border' => '10,5000,overlay']);

        $this->assertInstanceOf(ImageInterface::class, $border->run($image));
    }

    public function testRunShrink()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('width')->andReturn(100)->once();
            $mock->shouldReceive('height')->andReturn(100)->once();
            $mock->shouldReceive('resize')->with(80, 80)->andReturn($mock)->once();
            $mock->shouldReceive('resizeCanvasRelative')->with(20, 20, 'rgba(0, 0, 0, 0.5)', 'center')->andReturn($mock)->once();
        });

        $border = new Border();
        $border->setParams(['border' => '10,5000,shrink']);

        $this->assertInstanceOf(ImageInterface::class, $border->run($image));
    }

    public function testRunExpand()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('resizeCanvasRelative')->with(20, 20, 'rgba(0, 0, 0, 0.5)', 'center')->andReturn($mock)->once();
        });

        $border = new Border();
        $border->setParams(['border' => '10,5000,expand']);

        $this->assertInstanceOf(ImageInterface::class, $border->run($image));
    }
}
