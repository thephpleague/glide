<?php

namespace League\Glide\Manipulators;

use Mockery;

class BorderTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Border', new Border());
    }

    public function testRun()
    {
        $this->markTestIncomplete();
    }

    public function testGetBorder()
    {
        $image = Mockery::mock('Intervention\Image\Image');

        $border = new Border();

        $this->assertNull($border->getBorder($image));

        $this->assertSame(
            [10.0, 'rgba(0, 0, 0, 1)', 'overlay'],
            $border->setParams(['border' => '10,black'])->getBorder($image, 1, '100')
        );
    }

    public function testGetWidth()
    {
        $image = Mockery::mock('Intervention\Image\Image');

        $border = new Border();

        $this->assertSame(100.0, $border->getWidth($image, 1, '100'));
    }

    public function testGetColor()
    {
        $border = new Border();

        $this->assertSame('rgba(0, 0, 0, 1)', $border->getColor('black'));
    }

    public function testGetMethod()
    {
        $border = new Border();

        $this->assertSame('expand', $border->getMethod('expand'));
        $this->assertSame('shrink', $border->getMethod('shrink'));
        $this->assertSame('overlay', $border->getMethod('overlay'));
        $this->assertSame('overlay', $border->getMethod('invalid'));
    }

    public function testGetDpr()
    {
        $border = new Border();

        $this->assertSame(1.0, $border->setParams(['dpr' => 'invalid'])->getDpr());
        $this->assertSame(1.0, $border->setParams(['dpr' => '-1'])->getDpr());
        $this->assertSame(1.0, $border->setParams(['dpr' => '9'])->getDpr());
        $this->assertSame(2.0, $border->setParams(['dpr' => '2'])->getDpr());
    }

    public function testRunOverlay()
    {
        $this->markTestIncomplete();
    }

    public function testRunShrink()
    {
        $this->markTestIncomplete();
    }

    public function testRunExpand()
    {
        $this->markTestIncomplete();
    }
}
