<?php

namespace League\Glide\Manipulators;

use League\Glide\Requests\RequestFactory;
use Mockery;

class BlurTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Blur();
    }

    public function tearDown()
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
            $this->manipulator->run(RequestFactory::create(['image.jpg', ['blur' => '10']]), $image)
        );
    }

    public function testGetBlur()
    {
        $this->assertSame(50, $this->manipulator->getBlur('50'));
        $this->assertSame(50, $this->manipulator->getBlur(50));
        $this->assertSame(false, $this->manipulator->getBlur(null));
        $this->assertSame(false, $this->manipulator->getBlur('a'));
        $this->assertSame(false, $this->manipulator->getBlur('-1'));
        $this->assertSame(false, $this->manipulator->getBlur('101'));
    }
}
