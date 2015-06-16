<?php

namespace League\Glide\Manipulators;

use League\Glide\Requests\RequestFactory;
use Mockery;

class OutputTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Output();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Output', $this->manipulator);
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
                RequestFactory::create([
                    'image.jpg',
                    [
                        'fm' => 'jpg',
                        'q' => '100',
                    ],
                ]),
                $image
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
                RequestFactory::create([
                    'image.jpg',
                    [
                        'fm' => 'pjpg',
                    ],
                ]),
                $image
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

        $this->assertSame('jpg', $this->manipulator->getFormat($image, 'jpg'));
        $this->assertSame('png', $this->manipulator->getFormat($image, 'png'));
        $this->assertSame('gif', $this->manipulator->getFormat($image, 'gif'));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, null));
        $this->assertSame('png', $this->manipulator->getFormat($image, null));
        $this->assertSame('gif', $this->manipulator->getFormat($image, null));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, null));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ''));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, 'invalid'));
    }

    public function testGetQuality()
    {
        $this->assertSame(100, $this->manipulator->getQuality('100'));
        $this->assertSame(100, $this->manipulator->getQuality(100));
        $this->assertSame(90, $this->manipulator->getQuality(null));
        $this->assertSame(90, $this->manipulator->getQuality('a'));
        $this->assertSame(50, $this->manipulator->getQuality('50.50'));
        $this->assertSame(90, $this->manipulator->getQuality('-1'));
        $this->assertSame(90, $this->manipulator->getQuality('101'));
    }
}
