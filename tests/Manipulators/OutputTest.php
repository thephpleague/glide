<?php

namespace League\Glide\Manipulators;

use League\Glide\Request;
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
            new Request('image.jpg', [
                'fm' => 'jpg',
                'q' => '100',
            ]),
            $image
        );
    }

    public function testGetFormat()
    {
        $this->assertEquals('jpg', $this->manipulator->getFormat(null));
        $this->assertEquals('jpg', $this->manipulator->getFormat('jpg'));
        $this->assertEquals('png', $this->manipulator->getFormat('png'));
        $this->assertEquals('gif', $this->manipulator->getFormat('gif'));
        $this->assertEquals('jpg', $this->manipulator->getFormat(''));
        $this->assertEquals('jpg', $this->manipulator->getFormat('invalid'));
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
