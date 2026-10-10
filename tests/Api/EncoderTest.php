<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\MediaType;
use Mockery;
use PHPUnit\Framework\TestCase;

class EncoderTest extends TestCase
{
    private Encoder $encoder;
    private ImageInterface $jpg;
    private ImageInterface $png;
    private ImageInterface $gif;
    private ImageInterface $tif;
    private ImageInterface $webp;
    private ImageInterface $avif;
    private ImageInterface $heic;

    public function setUp(): void
    {
        $manager = ImageManager::usingDriver(GdDriver::class);

        $this->jpg = $manager->decode(
            $manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_JPEG)->toStream(),
        );
        $this->png = $manager->decode(
            $manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_PNG)->toStream(),
        );
        $this->gif = $manager->decode(
            $manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_GIF)->toStream(),
        );

        if (function_exists('imagecreatefromwebp')) {
            $this->webp = $manager->decode(
                $manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_WEBP)->toStream(),
            );
        }

        if (function_exists('imagecreatefromavif')) {
            $this->avif = $manager->decode(
                $manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_AVIF)->toStream(),
            );
        }

        $this->encoder = new Encoder();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Encoder::class, $this->encoder);
    }

    public function testRun(): void
    {
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->jpg)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->png)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->gif)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->jpg)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->png)));
        $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->gif)));
        $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->jpg)));
        $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->png)));
        $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->gif)));
        $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->jpg)));
        $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->png)));
        $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->gif)));

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->webp)));
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->webp)));
            $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->webp)));
            $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->webp)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->jpg)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->png)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->gif)));
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->webp)));
        }
        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'jpg'])->run($this->avif)));
            $this->assertSame('image/jpeg', $this->getMime($this->encoder->setParams(['fm' => 'pjpg'])->run($this->avif)));
            $this->assertSame('image/png', $this->getMime($this->encoder->setParams(['fm' => 'png'])->run($this->avif)));
            $this->assertSame('image/gif', $this->getMime($this->encoder->setParams(['fm' => 'gif'])->run($this->avif)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->jpg)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->png)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->gif)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->avif)));
        }

        if (function_exists('imagecreatefromwebp') && function_exists('imagecreatefromavif')) {
            $this->assertSame('image/webp', $this->getMime($this->encoder->setParams(['fm' => 'webp'])->run($this->avif)));
            $this->assertSame('image/avif', $this->getMime($this->encoder->setParams(['fm' => 'avif'])->run($this->webp)));
        }
    }

    public function testGetFormat(): void
    {
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => 'jpg'])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('png', $this->encoder->setParams(['fm' => 'png'])->getFormat($this->getImageByMimeType('image/png')));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => 'gif'])->getFormat($this->getImageByMimeType('image/gif')));

        // Make sure 'fm' parameter takes precedence
        $this->assertSame('png', $this->encoder->setParams(['fm' => 'png'])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => 'gif'])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('pjpg', $this->encoder->setParams(['fm' => 'pjpg'])->getFormat($this->getImageByMimeType('image/jpeg')));

        // Make sure we keep the current format if no format is provided
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('png', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/png')));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/gif')));

        $this->assertSame('jpg', $this->encoder->setParams(['fm' => ''])->getFormat($this->getImageByMimeType('image/jpeg')));
        $this->assertSame('png', $this->encoder->setParams(['fm' => ''])->getFormat($this->getImageByMimeType('image/png')));

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('webp', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/webp')));
            $this->assertSame('webp', $this->encoder->setParams(['fm' => 'webp'])->getFormat($this->getImageByMimeType('image/jpeg')));
        }

        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('avif', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/avif')));
            $this->assertSame('avif', $this->encoder->setParams(['fm' => 'avif'])->getFormat($this->getImageByMimeType('image/jpeg')));
        }

        $this->assertSame('jxl', $this->encoder->setParams(['fm' => null])->getFormat($this->getImageByMimeType('image/jxl')));
        $this->assertSame('jxl', $this->encoder->setParams(['fm' => 'jxl'])->getFormat($this->getImageByMimeType('image/jpeg')));
    }

    public function testGetFormatThrowsExceptionForInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid format provided: invalid');
        $this->encoder->setParams(['fm' => 'invalid'])->getFormat($this->getImageByMimeType('image/png'));
    }

    protected function getImageByMimeType(string $mimeType): ImageInterface
    {
        return Mockery::mock(ImageInterface::class, function ($mock) use ($mimeType) {
            $this->assertMediaType($mock, $mimeType);
        });
    }

    public function testGetQuality(): void
    {
        $this->assertSame(100, $this->encoder->setParams(['q' => '100'])->getQuality());
        $this->assertSame(100, $this->encoder->setParams(['q' => 100])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => null])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => 'a'])->getQuality());
        $this->assertSame(50, $this->encoder->setParams(['q' => '50.50'])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => '-1'])->getQuality());
        $this->assertSame(85, $this->encoder->setParams(['q' => '101'])->getQuality());
    }

    /**
     * Test functions that require the imagick extension.
     */
    public function testWithImagick(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped(
                'The imagick extension is not available.',
            );
        }
        $manager = ImageManager::usingDriver(ImagickDriver::class);
        // These need to be recreated with the imagick driver selected in the manager
        $this->jpg = $manager->decode($manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_JPEG)->toStream());
        $this->png = $manager->decode($manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_PNG)->toStream());
        $this->gif = $manager->decode($manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_GIF)->toStream());
        $this->heic = $manager->decode($manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_HEIC)->toStream());
        $this->tif = $manager->decode($manager->createImage(100, 100)->encodeUsingMediaType(MediaType::IMAGE_TIFF)->toStream());

        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->jpg)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->png)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->gif)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->heic)));

        $this->assertSame('image/jxl', $this->getMime($this->encoder->setParams(['fm' => 'jxl'])->run($this->jpg)));
        $this->assertSame('image/jxl', $this->getMime($this->encoder->setParams(['fm' => 'jxl'])->run($this->png)));
    }

    public function testSupportedFormats(): void
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
            'jxl' => 'image/jxl',
        ];

        $this->assertSame($expected, Encoder::supportedFormats());
    }

    protected function getMime(EncodedImageInterface $image): string
    {
        return $image->mediaType();
    }

    /**
     * Creates an assertion to check media type.
     *
     * @param Mock   $mock
     * @param string $mediaType
     */
    protected function assertMediaType($mock, $mediaType): Mockery\CompositeExpectation
    {
        /*
         * @var Mock $mock
         */
        return $mock->shouldReceive('origin')->andReturn(Mockery::mock('Intervention\Image\Origin', ['mediaType' => $mediaType]));
    }
}
