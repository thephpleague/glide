<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use PHPUnit\Framework\TestCase;

class PixelateTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Pixelate();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Pixelate', $this->manipulator);
    }

    public function testRun()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('pixelate')->with('10')->once();
        });

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->setParams(['pixel' => '10'])->run($image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->setParams(['pixel' => '50'])->getPixelate());
        $this->assertSame(50, $this->manipulator->setParams(['pixel' => 50.50])->getPixelate());
        $this->assertSame(null, $this->manipulator->setParams(['pixel' => null])->getPixelate());
        $this->assertSame(null, $this->manipulator->setParams(['pixel' => 'a'])->getPixelate());
        $this->assertSame(null, $this->manipulator->setParams(['pixel' => '-1'])->getPixelate());
        $this->assertSame(null, $this->manipulator->setParams(['pixel' => '1001'])->getPixelate());
    }
}
