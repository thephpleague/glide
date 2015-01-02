<?php

namespace Glide;

use Mockery;

class ApiTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('Glide\Api', new Api(Mockery::mock('Intervention\Image\ImageManager'), []));
    }

    public function testRun()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('getEncoded')->andReturn('encoded');
        });

        $manager = Mockery::mock('Intervention\Image\ImageManager', function ($mock) use ($image) {
            $mock->shouldReceive('make')->andReturn($image);
        });

        $manipulator = Mockery::mock('Glide\Interfaces\Manipulator', function ($mock) {
            $mock->shouldReceive('run')->andReturn(null);
        });

        $api = new Api($manager, [$manipulator]);

        $request = Mockery::mock('Glide\Request');

        $this->assertEquals('encoded', $api->run($request, 'source'));
    }
}
