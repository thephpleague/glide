<?php

namespace League\Glide\Manipulators;

use League\Glide\Factories\Request;
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
            $mock->shouldReceive('encode')->with('jpg', '100')->once();
        });

        $this->manipulator->run(
            Request::create('image.jpg', [
                'fm' => 'jpg',
                'q' => '100',
            ]),
            $image
        );
    }

    public function testGetFormat()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('mime')->andReturn('image/jpeg')->once();
            $mock->shouldReceive('mime')->andReturn('image/png')->once();
            $mock->shouldReceive('mime')->andReturn('image/gif')->once();
            $mock->shouldReceive('mime')->andReturn('image/bmp')->once();
        });

        $this->assertEquals('jpg', $this->manipulator->getFormat($image, null));
        $this->assertEquals('png', $this->manipulator->getFormat($image, null));
        $this->assertEquals('gif', $this->manipulator->getFormat($image, null));
        $this->assertEquals('jpg', $this->manipulator->getFormat($image, null));
        $this->assertEquals('jpg', $this->manipulator->getFormat($image, 'jpg'));
        $this->assertEquals('png', $this->manipulator->getFormat($image, 'png'));
        $this->assertEquals('gif', $this->manipulator->getFormat($image, 'gif'));
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
