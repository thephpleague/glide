<?php

namespace League\Glide\Manipulators;

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
        $this->assertInstanceOf('League\Glide\Manipulators\Gamma', $this->manipulator);
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('gamma')->with('1.5')->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->run($image, ['gam' => '1.5'])
        );
    }

    public function testGetGamma()
    {
        $this->assertSame(1.5, $this->manipulator->getGamma(['gam' => '1.5']));
        $this->assertSame(1.5, $this->manipulator->getGamma(['gam' => 1.5]));
        $this->assertSame(null, $this->manipulator->getGamma(['gam' => null]));
        $this->assertSame(null, $this->manipulator->getGamma(['gam' => 'a']));
        $this->assertSame(null, $this->manipulator->getGamma(['gam' => '.1']));
        $this->assertSame(null, $this->manipulator->getGamma(['gam' => '9.999']));
        $this->assertSame(null, $this->manipulator->getGamma(['gam' => '0.005']));
        $this->assertSame(null, $this->manipulator->getGamma(['gam' => '-1']));
    }
}
