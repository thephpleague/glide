<?php

namespace League\Glide\Manipulators\Helpers;

use Mockery;
use PHPUnit\Framework\TestCase;

class DimensionTest extends TestCase
{
    private $image;

    public function setUp(): void
    {
        $this->image = Mockery::mock('Intervention\Image\Image');
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testPixels()
    {
        $dimension = new Dimension($this->image);
        $this->assertSame(500.0, $dimension->get('500'));
    }

    public function testRelativeWidth()
    {
        $this->image->shouldReceive('width')->andReturn('100')->once();

        $dimension = new Dimension($this->image);
        $this->assertSame(5.0, $dimension->get('5w'));
    }

    public function testRelativeHeight()
    {
        $this->image->shouldReceive('height')->andReturn('100')->once();

        $dimension = new Dimension($this->image);
        $this->assertSame(5.0, $dimension->get('5h'));
    }

    public function testDevicePixelRatio()
    {
        $dimension = new Dimension($this->image, 2);
        $this->assertSame(1000.0, $dimension->get('500'));
    }

    public function testInvalidInputs()
    {
        $dimension = new Dimension($this->image);
        $this->assertSame(null, $dimension->get('invalid'));
        $this->assertSame(null, $dimension->get('0'));
        $this->assertSame(null, $dimension->get('-1'));
    }
}
