<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use PHPUnit\Framework\TestCase;

class BrightnessTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Brightness();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Brightness', $this->manipulator);
    }

    public function testRun()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('brightness')->with('50')->once();
        });

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->setParams(['bri' => 50])->run($image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->setParams(['bri' => '50'])->getBrightness());
        $this->assertSame(50, $this->manipulator->setParams(['bri' => 50])->getBrightness());
        $this->assertSame(null, $this->manipulator->setParams(['bri' => null])->getBrightness());
        $this->assertSame(null, $this->manipulator->setParams(['bri' => '101'])->getBrightness());
        $this->assertSame(null, $this->manipulator->setParams(['bri' => '-101'])->getBrightness());
        $this->assertSame(null, $this->manipulator->setParams(['bri' => 'a'])->getBrightness());
    }
}
