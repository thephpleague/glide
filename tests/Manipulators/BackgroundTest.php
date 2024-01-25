<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Origin;
use PHPUnit\Framework\TestCase;

class BackgroundTest extends TestCase
{
    private $manipulator;

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Background', new Background());
    }

    public function testRun()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $originMock = \Mockery::mock(Origin::class, ['mediaType' => 'image/jpeg']);

            $mock->shouldReceive('width')->andReturn(100)->once();
            $mock->shouldReceive('height')->andReturn(100)->once();
            $mock->shouldReceive('origin')->andReturn($originMock)->once();

            $mock->shouldReceive('driver')->andReturn(\Mockery::mock(DriverInterface::class, function ($mock) use ($originMock) {
                $mock->shouldReceive('createImage')->with(100, 100)->andReturn(\Mockery::mock(ImageInterface::class, function ($mock) use ($originMock) {
                    $mock->shouldReceive('fill')->with('rgba(0, 0, 0, 1)')->andReturn(\Mockery::mock(ImageInterface::class, function ($mock) use ($originMock) {
                        $mock->shouldReceive('setOrigin')->withArgs(function ($arg1) {
                            return $arg1 instanceof Origin;
                        })->andReturn($mock)->once();
                        $mock->shouldReceive('place')->andReturn($mock)->once();
                    }))->once();
                }))->once();
            }))->once();
        });

        $border = new Background();

        $this->assertInstanceOf(ImageInterface::class, $border->run($image));
        $this->assertInstanceOf(ImageInterface::class, $border->setParams(['bg' => 'black'])->run($image));
    }
}
