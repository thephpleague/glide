<?php

namespace League\Glide\Manipulators;

use Mockery;
use PHPUnit\Framework\TestCase;

class TrimTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Trim();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Trim', new Trim());
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('trim')->andReturn($mock)->with('top-left', ['top', 'bottom', 'left', 'right'], 20, 10)->once();
            $mock->shouldReceive('trim')->andReturn($mock)->with('top-left', null, null, null)->once();
        });

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['trim' => 'top-left,trbl,20,10'])->run($image)
        );

        $this->assertInstanceOf(
            'Intervention\Image\Image',
            $this->manipulator->setParams(['trim' => 'invalid,invalid,150,invalid'])->run($image)
        );
    }

    public function testGetTrim()
    {
        $this->assertSame(
            ['top-left', ['top', 'bottom', 'left', 'right'], 20, 10],
            $this->manipulator->setParams(['trim' => 'top-left,trbl,20,10'])->getTrim()
        );

        $this->assertSame(
            ['top-left', null, null, null],
            $this->manipulator->setParams(['trim' => 'invalid,invalid,150,invalid'])->getTrim()
        );

        $this->assertSame(
            ['top-left', null, 0, 0],
            $this->manipulator->setParams(['trim' => 'top-left,,0,0'])->getTrim()
        );
    }

    public function testGetBase()
    {
        $this->assertSame('top-left', $this->manipulator->getBase('top-left'));
        $this->assertSame('bottom-right', $this->manipulator->getBase('bottom-right'));
        $this->assertSame('transparent', $this->manipulator->getBase('transparent'));
        $this->assertSame('top-left', $this->manipulator->getBase(null));
        $this->assertSame('top-left', $this->manipulator->getBase(123));
    }

    public function testGetAway()
    {
        $this->assertSame(['top', 'bottom', 'left', 'right'], $this->manipulator->getAway('tblr'));
        $this->assertSame(['top', 'bottom', 'left'], $this->manipulator->getAway('tbl'));
        $this->assertSame(['top', 'bottom'], $this->manipulator->getAway('tb'));
        $this->assertSame(['top'], $this->manipulator->getAway('t'));
        $this->assertSame(['bottom', 'left'], $this->manipulator->getAway('bl'));
        $this->assertSame(['top', 'right'], $this->manipulator->getAway('tr'));
        $this->assertSame(['top', 'bottom', 'left', 'right'], $this->manipulator->getAway('rlbt'));
        $this->assertSame(['top', 'bottom', 'left', 'right'], $this->manipulator->getAway('rrllbbtt'));
        $this->assertSame(null, $this->manipulator->getAway('invalid'));
        $this->assertSame(null, $this->manipulator->getAway(null));
        $this->assertSame(null, $this->manipulator->getAway(123));
    }

    public function testTolerance()
    {
        $this->assertSame(20, $this->manipulator->getTolerance(20));
        $this->assertSame(20, $this->manipulator->getTolerance('20'));
        $this->assertSame(0, $this->manipulator->getTolerance('0'));
        $this->assertSame(100, $this->manipulator->getTolerance('100'));
        $this->assertSame(null, $this->manipulator->getTolerance('150'));
        $this->assertSame(null, $this->manipulator->getTolerance('-150'));
        $this->assertSame(null, $this->manipulator->getTolerance('invalid'));
    }

    public function testFeather()
    {
        $this->assertSame(20, $this->manipulator->getFeather(20));
        $this->assertSame(-20, $this->manipulator->getFeather(-20));
        $this->assertSame(20, $this->manipulator->getFeather('20'));
        $this->assertSame(0, $this->manipulator->getFeather('0'));
        $this->assertSame(100, $this->manipulator->getFeather('100'));
        $this->assertSame(150, $this->manipulator->getFeather('150'));
        $this->assertSame(-150, $this->manipulator->getFeather('-150'));
        $this->assertSame(null, $this->manipulator->getFeather('invalid'));
    }
}
