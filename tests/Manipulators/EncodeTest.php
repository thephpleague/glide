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
        $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'jpg'])->run($this->jpg)->mime);
        $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'jpg'])->run($this->png)->mime);
        $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'jpg'])->run($this->gif)->mime);
        $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'pjpg'])->run($this->jpg)->mime);
        $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'pjpg'])->run($this->png)->mime);
        $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'pjpg'])->run($this->gif)->mime);
        $this->assertSame('image/png', $this->manipulator->setParams(['fm' => 'png'])->run($this->jpg)->mime);
        $this->assertSame('image/png', $this->manipulator->setParams(['fm' => 'png'])->run($this->png)->mime);
        $this->assertSame('image/png', $this->manipulator->setParams(['fm' => 'png'])->run($this->gif)->mime);
        $this->assertSame('image/gif', $this->manipulator->setParams(['fm' => 'gif'])->run($this->jpg)->mime);
        $this->assertSame('image/gif', $this->manipulator->setParams(['fm' => 'gif'])->run($this->png)->mime);
        $this->assertSame('image/gif', $this->manipulator->setParams(['fm' => 'gif'])->run($this->gif)->mime);
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

        $this->assertSame('jpg', $this->manipulator->setParams(['fm' => 'jpg'])->getFormat($image));
        $this->assertSame('png', $this->manipulator->setParams(['fm' => 'png'])->getFormat($image));
        $this->assertSame('gif', $this->manipulator->setParams(['fm' => 'gif'])->getFormat($image));
        $this->assertSame('jpg', $this->manipulator->setParams(['fm' => null])->getFormat($image));
        $this->assertSame('png', $this->manipulator->setParams(['fm' => null])->getFormat($image));
        $this->assertSame('gif', $this->manipulator->setParams(['fm' => null])->getFormat($image));
        $this->assertSame('jpg', $this->manipulator->setParams(['fm' => null])->getFormat($image));
        $this->assertSame('jpg', $this->manipulator->setParams(['fm' => ''])->getFormat($image));
        $this->assertSame('jpg', $this->manipulator->setParams(['fm' => 'invalid'])->getFormat($image));
    }

    public function testGetQuality()
    {
        $this->assertSame(100, $this->manipulator->setParams(['q' => '100'])->getQuality());
        $this->assertSame(100, $this->manipulator->setParams(['q' => 100])->getQuality());
        $this->assertSame(90, $this->manipulator->setParams(['q' => null])->getQuality());
        $this->assertSame(90, $this->manipulator->setParams(['q' => 'a'])->getQuality());
        $this->assertSame(50, $this->manipulator->setParams(['q' => '50.50'])->getQuality());
        $this->assertSame(90, $this->manipulator->setParams(['q' => '-1'])->getQuality());
        $this->assertSame(90, $this->manipulator->setParams(['q' => '101'])->getQuality());
    }
}
