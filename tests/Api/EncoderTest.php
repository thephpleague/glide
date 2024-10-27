<?php

namespace League\Glide\Api;

use Intervention\Image\Encoders\MediaTypeEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
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

        $this->encoder = new Encoder();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance(): void
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         */
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
        /**
         * @psalm-suppress MissingClosureParamType
         */
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            /*
             * @var Mock $mock
             */
            $this->assertMediaType($mock, 'image/jpeg')->once();
            $this->assertMediaType($mock, 'image/png')->once();
            $this->assertMediaType($mock, 'image/gif')->once();
            $this->assertMediaType($mock, 'image/bmp')->once();
            $this->assertMediaType($mock, 'image/jpeg')->twice();

            if (function_exists('imagecreatefromwebp')) {
                $this->assertMediaType($mock, 'image/webp')->once();
            }

            if (function_exists('imagecreatefromavif')) {
                $this->assertMediaType($mock, 'image/avif')->once();
            }
        });

        $this->assertSame('jpg', $this->encoder->setParams(['fm' => 'jpg'])->getFormat($image));
        $this->assertSame('png', $this->encoder->setParams(['fm' => 'png'])->getFormat($image));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => 'gif'])->getFormat($image));
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => null])->getFormat($image));
        $this->assertSame('png', $this->encoder->setParams(['fm' => null])->getFormat($image));
        $this->assertSame('gif', $this->encoder->setParams(['fm' => null])->getFormat($image));
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => null])->getFormat($image));

        $this->assertSame('jpg', $this->encoder->setParams(['fm' => ''])->getFormat($image));
        $this->assertSame('jpg', $this->encoder->setParams(['fm' => 'invalid'])->getFormat($image));

        if (function_exists('imagecreatefromwebp')) {
            $this->assertSame('webp', $this->encoder->setParams(['fm' => null])->getFormat($image));
        }

        if (function_exists('imagecreatefromavif')) {
            $this->assertSame('avif', $this->encoder->setParams(['fm' => null])->getFormat($image));
        }
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

        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->jpg)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->png)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->gif)));
        $this->assertSame('image/tiff', $this->getMime($this->encoder->setParams(['fm' => 'tiff'])->run($this->heic)));
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
     *
     * @psalm-suppress MoreSpecificReturnType
     */
    protected function assertMediaType($mock, $mediaType): Mockery\CompositeExpectation
    {
        /*
         * @var Mock $mock
         */
        /**
         * @psalm-suppress LessSpecificReturnStatement, UndefinedMagicMethod
         */
        return $mock->shouldReceive('origin')->andReturn(\Mockery::mock('Intervention\Image\Origin', ['mediaType' => $mediaType]));
    }
}
