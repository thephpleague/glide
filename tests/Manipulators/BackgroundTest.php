<?php

namespace League\Glide\Manipulators;

use Mockery;

class BackgroundTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Background', new Background());
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('width')->andReturn(100)->once();
            $mock->shouldReceive('height')->andReturn(100)->once();
            $mock->shouldReceive('getDriver')->andReturn(Mockery::mock('Intervention\Image\AbstractDriver', function ($mock) {
                $mock->shouldReceive('newImage')->with(100, 100, 'rgba(0, 0, 0, 1)')->andReturn(Mockery::mock('Intervention\Image\Image', function ($mock) {
                    $mock->shouldReceive('insert')->andReturn($mock)->once();
                }))->once();
            }))->once();
        });

        $border = new Background();

        $this->assertInstanceOf('Intervention\Image\Image', $border->run($image));
        $this->assertInstanceOf('Intervention\Image\Image', $border->setParams(['bg' => 'black'])->run($image));
    }
}
