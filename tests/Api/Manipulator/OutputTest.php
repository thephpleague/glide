<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
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
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Output', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('encode')->with('jpg', '100')->andReturn($mock)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(
                RequestFactory::create('image.jpg', [
                    'fm' => 'jpg',
                    'q' => '100',
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

        $this->assertEquals('jpg', $this->manipulator->getFormat($image, 'jpg'));
        $this->assertEquals('png', $this->manipulator->getFormat($image, 'png'));
        $this->assertEquals('gif', $this->manipulator->getFormat($image, 'gif'));
        $this->assertEquals('jpg', $this->manipulator->getFormat($image, null));
        $this->assertEquals('png', $this->manipulator->getFormat($image, null));
        $this->assertEquals('gif', $this->manipulator->getFormat($image, null));
        $this->assertEquals('jpg', $this->manipulator->getFormat($image, null));
        $this->assertEquals('jpg', $this->manipulator->getFormat($image, ''));
        $this->assertEquals('jpg', $this->manipulator->getFormat($image, 'invalid'));
    }

    public function testGetQuality()
    {
        $this->assertEquals('100', $this->manipulator->getQuality('100'));
        $this->assertEquals('90', $this->manipulator->getQuality(null));
        $this->assertEquals('90', $this->manipulator->getQuality('a'));
        $this->assertEquals('90', $this->manipulator->getQuality('50.50'));
        $this->assertEquals('90', $this->manipulator->getQuality('-1'));
        $this->assertEquals('90', $this->manipulator->getQuality('101'));
    }
}
