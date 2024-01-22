<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Encoders\MediaTypeEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class EncodeTest extends TestCase
{
    private $manipulator;
    private $jpg;
    private $png;
    private $gif;
    private $tif;
    private $webp;
    private $avif;

    public function setUp(): void
    {
        $manager = ImageManager::gd();
        $this->jpg = $manager->create(100, 100)->encode(new MediaTypeEncoder('image/jpeg'));
        $this->png = $manager->create(100, 100)->encode(new MediaTypeEncoder('image/png'));
        $this->gif = $manager->create(100, 100)->encode(new MediaTypeEncoder('image/gif'));

        if (function_exists('imagecreatefromwebp')) {
            $this->webp = $manager->create(100, 100)->encode(new MediaTypeEncoder('webp'));
        }

        if (function_exists('imagecreatefromavif')) {
            $this->avif = $manager->create(100, 100)->encode(new MediaTypeEncoder('avif'));
        }

        $this->manipulator = new Encode();
    }

    public function tearDown(): void
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

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'jpg'])->run($this->webp)->mime);
            $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'pjpg'])->run($this->webp)->mime);
            $this->assertSame('image/png', $this->manipulator->setParams(['fm' => 'png'])->run($this->webp)->mime);
            $this->assertSame('image/gif', $this->manipulator->setParams(['fm' => 'gif'])->run($this->webp)->mime);
            $this->assertSame('image/webp', $this->manipulator->setParams(['fm' => 'webp'])->run($this->jpg)->mime);
            $this->assertSame('image/webp', $this->manipulator->setParams(['fm' => 'webp'])->run($this->png)->mime);
            $this->assertSame('image/webp', $this->manipulator->setParams(['fm' => 'webp'])->run($this->gif)->mime);
            $this->assertSame('image/webp', $this->manipulator->setParams(['fm' => 'webp'])->run($this->webp)->mime);
        }
        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'jpg'])->run($this->avif)->mime);
            $this->assertSame('image/jpeg', $this->manipulator->setParams(['fm' => 'pjpg'])->run($this->avif)->mime);
            $this->assertSame('image/png', $this->manipulator->setParams(['fm' => 'png'])->run($this->avif)->mime);
            $this->assertSame('image/gif', $this->manipulator->setParams(['fm' => 'gif'])->run($this->avif)->mime);
            $this->assertSame('image/avif', $this->manipulator->setParams(['fm' => 'avif'])->run($this->jpg)->mime);
            $this->assertSame('image/avif', $this->manipulator->setParams(['fm' => 'avif'])->run($this->png)->mime);
            $this->assertSame('image/avif', $this->manipulator->setParams(['fm' => 'avif'])->run($this->gif)->mime);
            $this->assertSame('image/avif', $this->manipulator->setParams(['fm' => 'avif'])->run($this->avif)->mime);
        }

        if (function_exists('imagecreatefromwebp') && function_exists('imagecreatefromavif')) {
            $this->assertSame('image/webp', $this->manipulator->setParams(['fm' => 'webp'])->run($this->avif)->mime);
            $this->assertSame('image/avif', $this->manipulator->setParams(['fm' => 'avif'])->run($this->webp)->mime);
        }
    }

    public function testGetFormat()
    {
        $image = Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('mime')->andReturn('image/jpeg')->once();
            $mock->shouldReceive('mime')->andReturn('image/png')->once();
            $mock->shouldReceive('mime')->andReturn('image/gif')->once();
            $mock->shouldReceive('mime')->andReturn('image/bmp')->once();
            $mock->shouldReceive('mime')->andReturn('image/jpeg')->twice();

            if (function_exists('imagecreatefromwebp')) {
                $mock->shouldReceive('mime')->andReturn('image/webp')->once();
            }
            if (function_exists('imagecreatefromavif')) {
                $mock->shouldReceive('mime')->andReturn('image/avif')->once();
            }
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

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('webp', $this->manipulator->setParams(['fm' => null])->getFormat($image));
        }

        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('avif', $this->manipulator->setParams(['fm' => null])->getFormat($image));
        }
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

    /**
     * Test functions that require the imagick extension.
     *
     * @return void
     */
    public function testWithImagick()
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped(
                'The imagick extension is not available.'
            );
        }
        $manager = ImageManager::imagick();
        //These need to be recreated with the imagick driver selected in the manager
        $this->jpg = $manager->create(100, 100)->encode(new MediaTypeEncoder('image/jpeg'));
        $this->png = $manager->create(100, 100)->encode(new MediaTypeEncoder('image/png'));
        $this->gif = $manager->create(100, 100)->encode(new MediaTypeEncoder('image/gif'));
        $this->tif = $manager->create(100, 100)->encode(new MediaTypeEncoder('image/tiff'));

        $this->assertSame('image/tiff', $this->manipulator->setParams(['fm' => 'tiff'])->run($this->jpg)->mime);
        $this->assertSame('image/tiff', $this->manipulator->setParams(['fm' => 'tiff'])->run($this->png)->mime);
        $this->assertSame('image/tiff', $this->manipulator->setParams(['fm' => 'tiff'])->run($this->gif)->mime);
    }
}
