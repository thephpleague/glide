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
            $this->manipulator->setParams(['gam' => '1.5'])->run($image)
        );
    }

    public function testGetGamma()
    {
        $this->assertSame(1.5, $this->manipulator->setParams(['gam' => '1.5'])->getGamma());
        $this->assertSame(1.5, $this->manipulator->setParams(['gam' => 1.5])->getGamma());
        $this->assertSame(null, $this->manipulator->setParams(['gam' => null])->getGamma());
        $this->assertSame(null, $this->manipulator->setParams(['gam' => 'a'])->getGamma());
        $this->assertSame(null, $this->manipulator->setParams(['gam' => '.1'])->getGamma());
        $this->assertSame(null, $this->manipulator->setParams(['gam' => '9.999'])->getGamma());
        $this->assertSame(null, $this->manipulator->setParams(['gam' => '0.005'])->getGamma());
        $this->assertSame(null, $this->manipulator->setParams(['gam' => '-1'])->getGamma());
    }
}
