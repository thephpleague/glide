<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\ImageInterface;
use PHPUnit\Framework\TestCase;

class ContrastTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Contrast();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Contrast', $this->manipulator);
    }

    public function testRun()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('contrast')->with('50')->once();
        });

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->setParams(['con' => 50])->run($image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->setParams(['con' => '50'])->getContrast());
        $this->assertSame(50, $this->manipulator->setParams(['con' => 50])->getContrast());
        $this->assertSame(null, $this->manipulator->setParams(['con' => null])->getContrast());
        $this->assertSame(null, $this->manipulator->setParams(['con' => '101'])->getContrast());
        $this->assertSame(null, $this->manipulator->setParams(['con' => '-101'])->getContrast());
        $this->assertSame(null, $this->manipulator->setParams(['con' => 'a'])->getContrast());
    }
}
