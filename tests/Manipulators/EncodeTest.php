<?php

namespace League\Glide\Manipulators;

use Mockery;

class EncodeTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Encode();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Encode', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('encode')
                 ->with('jpg', '100')
                 ->andReturn($mock)
                 ->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(
                $image,
                [
                    'fm' => 'jpg',
                    'q' => '100',
                ]
            )
        );
    }

    public function testProgressiveJpeg()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('interlace')
                    ->once()
                 ->shouldReceive('encode')
                    ->with('jpg', '90')
                    ->andReturn($mock)
                    ->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(
                $image,
                [
                    'fm' => 'pjpg',
                ]
            )
        );
    }

    public function testGetFormat()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('mime')->andReturn('image/jpeg')->once();
            $mock->shouldReceive('mime')->andReturn('image/png')->once();
            $mock->shouldReceive('mime')->andReturn('image/gif')->once();
            $mock->shouldReceive('mime')->andReturn('image/bmp')->once();
            $mock->shouldReceive('mime')->andReturn('image/jpeg')->twice();
        });

        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => 'jpg']));
        $this->assertSame('png', $this->manipulator->getFormat($image, ['fm' => 'png']));
        $this->assertSame('gif', $this->manipulator->getFormat($image, ['fm' => 'gif']));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('png', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('gif', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => '']));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => 'invalid']));
    }

    public function testGetQuality()
    {
        $this->assertSame(100, $this->manipulator->getQuality(['q' => '100']));
        $this->assertSame(100, $this->manipulator->getQuality(['q' => 100]));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => null]));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => 'a']));
        $this->assertSame(50, $this->manipulator->getQuality(['q' => '50.50']));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => '-1']));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => '101']));
    }
}
