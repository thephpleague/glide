<?php

namespace League\Glide\Manipulators;

use League\Glide\Requests\RequestFactory;
use Mockery;

class ContrastTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Contrast();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Contrast', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('contrast')->with('50')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create(['image.jpg', ['con' => '50']]), $image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertSame(50, $this->manipulator->getContrast('50'));
        $this->assertSame(50, $this->manipulator->getContrast(50));
        $this->assertSame(false, $this->manipulator->getContrast(null));
        $this->assertSame(false, $this->manipulator->getContrast('101'));
        $this->assertSame(false, $this->manipulator->getContrast('-101'));
        $this->assertSame(false, $this->manipulator->getContrast('a'));
    }
}
