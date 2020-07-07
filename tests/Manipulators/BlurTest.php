<?php

namespace League\Glide\Manipulators;

use Mockery;
use PHPUnit\Framework\TestCase;

class BlurTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Blur();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Blur', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('blur')->with('10')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['blur' => 10])->run($image)
        );
    }

    public function testGetBlur()
    {
        $this->assertSame(50, $this->manipulator->setParams(['blur' => '50'])->getBlur());
        $this->assertSame(50, $this->manipulator->setParams(['blur' => 50])->getBlur());
        $this->assertSame(null, $this->manipulator->setParams(['blur' => null])->getBlur());
        $this->assertSame(null, $this->manipulator->setParams(['blur' => 'a'])->getBlur());
        $this->assertSame(null, $this->manipulator->setParams(['blur' => '-1'])->getBlur());
        $this->assertSame(null, $this->manipulator->setParams(['blur' => '101'])->getBlur());
    }
}
