<?php

namespace League\Glide\Manipulators;

use Intervention\Image\Encoders\MediaTypeEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
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
    private $heic;

    public function setUp(): void
    {
        $manager = ImageManager::gd();
        $this->jpg = $manager->read(
            $manager->create(100, 100)->encode(new MediaTypeEncoder('image/jpeg'))->toFilePointer()
        );
        $this->png = $manager->read(
            $manager->create(100, 100)->encode(new MediaTypeEncoder('image/png'))->toFilePointer()
        );
        $this->gif = $manager->read(
            $manager->create(100, 100)->encode(new MediaTypeEncoder('image/gif'))->toFilePointer()
        );

        if (function_exists('imagecreatefromwebp')) {
            $this->webp = $manager->read(
                $manager->create(100, 100)->encode(new MediaTypeEncoder('image/webp'))->toFilePointer()
            );
        }

        if (function_exists('imagecreatefromavif')) {
            $this->avif = $manager->read(
                $manager->create(100, 100)->encode(new MediaTypeEncoder('image/avif'))->toFilePointer()
            );
        }

        $this->manipulator = new Encode();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf('League\Glide\Manipulators\Encode', $this->manipulator);
    }

    public function testRun()
    {
        $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'jpg'])->run($this->jpg)));
        $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'jpg'])->run($this->png)));
        $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'jpg'])->run($this->gif)));
        $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'pjpg'])->run($this->jpg)));
        $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'pjpg'])->run($this->png)));
        $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'pjpg'])->run($this->gif)));
        $this->assertSame('image/png', $this->getMime($this->manipulator->setParams(['fm' => 'png'])->run($this->jpg)));
        $this->assertSame('image/png', $this->getMime($this->manipulator->setParams(['fm' => 'png'])->run($this->png)));
        $this->assertSame('image/png', $this->getMime($this->manipulator->setParams(['fm' => 'png'])->run($this->gif)));
        $this->assertSame('image/gif', $this->getMime($this->manipulator->setParams(['fm' => 'gif'])->run($this->jpg)));
        $this->assertSame('image/gif', $this->getMime($this->manipulator->setParams(['fm' => 'gif'])->run($this->png)));
        $this->assertSame('image/gif', $this->getMime($this->manipulator->setParams(['fm' => 'gif'])->run($this->gif)));

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'jpg'])->run($this->webp)));
            $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'pjpg'])->run($this->webp)));
            $this->assertSame('image/png', $this->getMime($this->manipulator->setParams(['fm' => 'png'])->run($this->webp)));
            $this->assertSame('image/gif', $this->getMime($this->manipulator->setParams(['fm' => 'gif'])->run($this->webp)));
            $this->assertSame('image/webp', $this->getMime($this->manipulator->setParams(['fm' => 'webp'])->run($this->jpg)));
            $this->assertSame('image/webp', $this->getMime($this->manipulator->setParams(['fm' => 'webp'])->run($this->png)));
            $this->assertSame('image/webp', $this->getMime($this->manipulator->setParams(['fm' => 'webp'])->run($this->gif)));
            $this->assertSame('image/webp', $this->getMime($this->manipulator->setParams(['fm' => 'webp'])->run($this->webp)));
        }
        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'jpg'])->run($this->avif)));
            $this->assertSame('image/jpeg', $this->getMime($this->manipulator->setParams(['fm' => 'pjpg'])->run($this->avif)));
            $this->assertSame('image/png', $this->getMime($this->manipulator->setParams(['fm' => 'png'])->run($this->avif)));
            $this->assertSame('image/gif', $this->getMime($this->manipulator->setParams(['fm' => 'gif'])->run($this->avif)));
            $this->assertSame('image/avif', $this->getMime($this->manipulator->setParams(['fm' => 'avif'])->run($this->jpg)));
            $this->assertSame('image/avif', $this->getMime($this->manipulator->setParams(['fm' => 'avif'])->run($this->png)));
            $this->assertSame('image/avif', $this->getMime($this->manipulator->setParams(['fm' => 'avif'])->run($this->gif)));
            $this->assertSame('image/avif', $this->getMime($this->manipulator->setParams(['fm' => 'avif'])->run($this->avif)));
        }

        if (function_exists('imagecreatefromwebp') && function_exists('imagecreatefromavif')) {
            $this->assertSame('image/webp', $this->getMime($this->manipulator->setParams(['fm' => 'webp'])->run($this->avif)));
            $this->assertSame('image/avif', $this->getMime($this->manipulator->setParams(['fm' => 'avif'])->run($this->webp)));
        }
    }

    public function testGetFormat()
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => 'image/jpeg']))->once();
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => 'image/png']))->once();
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => 'image/gif']))->once();
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => 'image/bmp']))->once();
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => 'image/jpeg']))->twice();

            if (function_exists('imagecreatefromwebp')) {
                $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => 'image/webp']))->once();
            }

            if (function_exists('imagecreatefromavif')) {
                $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => 'image/avif']))->once();
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
        // These need to be recreated with the imagick driver selected in the manager
        $this->jpg = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/jpeg'))->toFilePointer());
        $this->png = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/png'))->toFilePointer());
        $this->gif = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/gif'))->toFilePointer());
        $this->heic = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/heic'))->toFilePointer());
        $this->tif = $manager->read($manager->create(100, 100)->encode(new MediaTypeEncoder('image/tiff'))->toFilePointer());

        $this->assertSame('image/tiff', $this->getMime($this->manipulator->setParams(['fm' => 'tiff'])->run($this->jpg)));
        $this->assertSame('image/tiff', $this->getMime($this->manipulator->setParams(['fm' => 'tiff'])->run($this->png)));
        $this->assertSame('image/tiff', $this->getMime($this->manipulator->setParams(['fm' => 'tiff'])->run($this->gif)));
        $this->assertSame('image/tiff', $this->getMime($this->manipulator->setParams(['fm' => 'tiff'])->run($this->heic)));
    }

    public function testSupportedFormats()
    {
        $expected = [
            'avif' => 'image/avif',
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'pjpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'tiff' => 'image/tiff',
            'heic' => 'image/heic',
        ];

        $this->assertSame($expected, Encode::supportedFormats());
    }

    protected function getMime(ImageInterface $image)
    {
        return $image->origin()->mediaType();
    }
}
