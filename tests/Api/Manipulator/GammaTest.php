<?php

namespace League\Glide\Api\Manipulator;

use League\Glide\Http\RequestFactory;
use Mockery;

class GammaTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function setUp()
    {
        $this->manipulator = new Gamma();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Api\Manipulator\Gamma', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('gamma')->with('1.5')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run(RequestFactory::create('image.jpg', ['gam' => '1.5']), $image)
        );
    }

    public function testGetPixelate()
    {
        $this->assertEquals('1.5', $this->manipulator->getGamma('1.5'));
        $this->assertEquals(false, $this->manipulator->getGamma(null));
        $this->assertEquals(false, $this->manipulator->getGamma('a'));
        $this->assertEquals(false, $this->manipulator->getGamma('.1'));
        $this->assertEquals(false, $this->manipulator->getGamma('9.999'));
        $this->assertEquals(false, $this->manipulator->getGamma('0.005'));
        $this->assertEquals(false, $this->manipulator->getGamma('-1'));
    }
}
