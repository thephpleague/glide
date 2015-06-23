<?php

namespace League\Glide\Manipulators;

use Intervention\Image\ImageManager;
use Mockery;

class EncodeTest extends \PHPUnit_Framework_TestCase
{
    private $manipulator;
    private $jpg;
    private $png;
    private $gif;
    private $tif;

    public function setUp()
    {
        $manager = new ImageManager();
        $this->jpg = $manager->canvas(100, 100)->encode('jpg');
        $this->png = $manager->canvas(100, 100)->encode('png');
        $this->gif = $manager->canvas(100, 100)->encode('gif');

        $this->manipulator = new Encode();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Encode', $this->manipulator);
    }

    public function testRun()
    {
        $this->assertSame('image/jpeg', $this->manipulator->run($this->jpg, ['fm' => 'jpg'])->mime);
        $this->assertSame('image/jpeg', $this->manipulator->run($this->png, ['fm' => 'jpg'])->mime);
        $this->assertSame('image/jpeg', $this->manipulator->run($this->gif, ['fm' => 'jpg'])->mime);
        $this->assertSame('image/jpeg', $this->manipulator->run($this->jpg, ['fm' => 'pjpg'])->mime);
        $this->assertSame('image/jpeg', $this->manipulator->run($this->png, ['fm' => 'pjpg'])->mime);
        $this->assertSame('image/jpeg', $this->manipulator->run($this->gif, ['fm' => 'pjpg'])->mime);
        $this->assertSame('image/png', $this->manipulator->run($this->jpg, ['fm' => 'png'])->mime);
        $this->assertSame('image/png', $this->manipulator->run($this->png, ['fm' => 'png'])->mime);
        $this->assertSame('image/png', $this->manipulator->run($this->gif, ['fm' => 'png'])->mime);
        $this->assertSame('image/gif', $this->manipulator->run($this->jpg, ['fm' => 'gif'])->mime);
        $this->assertSame('image/gif', $this->manipulator->run($this->png, ['fm' => 'gif'])->mime);
        $this->assertSame('image/gif', $this->manipulator->run($this->gif, ['fm' => 'gif'])->mime);
    }

    public function testGetFormat()
    {
        $image = Mockery::mock('Intervention\Image\Image', function ($mock) {
            $mock->shouldReceive('mime')->andReturn('image/jpeg')->once();
            $mock->shouldReceive('mime')->andReturn('image/png')->once();
            $mock->shouldReceive('mime')->andReturn('image/gif')->once();
            $mock->shouldReceive('mime')->andReturn('image/bmp')->once();
            $mock->shouldReceive('mime')->andReturn('image/jpeg')->twice();
        });

        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => 'jpg']));
        $this->assertSame('png', $this->manipulator->getFormat($image, ['fm' => 'png']));
        $this->assertSame('gif', $this->manipulator->getFormat($image, ['fm' => 'gif']));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('png', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('gif', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => null]));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => '']));
        $this->assertSame('jpg', $this->manipulator->getFormat($image, ['fm' => 'invalid']));
    }

    public function testGetQuality()
    {
        $this->assertSame(100, $this->manipulator->getQuality(['q' => '100']));
        $this->assertSame(100, $this->manipulator->getQuality(['q' => 100]));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => null]));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => 'a']));
        $this->assertSame(50, $this->manipulator->getQuality(['q' => '50.50']));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => '-1']));
        $this->assertSame(90, $this->manipulator->getQuality(['q' => '101']));
    }
}
