<?php

namespace League\Glide\Manipulators;

use Mockery;
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
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Brightness', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('brightness')->with('50')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
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
