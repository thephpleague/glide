<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\Crop;
use League\Glide\Manipulators\Orientation;
use League\Glide\Manipulators\Size;
use PHPUnit\Framework\TestCase;

class DecoderTest extends TestCase
{
    use CreatesJpegWithOrientation;

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Decoder::class, new Decoder());
    }

    public function testGetParam(): void
    {
        $decoder = new Decoder(['w' => '100']);

        $this->assertSame('100', $decoder->getParam('w'));
        $this->assertNull($decoder->getParam('h'));
        $this->assertSame('200', $decoder->setParams(['w' => '200'])->getParam('w'));
    }

    public function testRunDecodesFullSizeWithoutResize(): void
    {
        $manager = ImageManager::usingDriver(GdDriver::class);
        $image = (new Decoder(['w' => '100']))->run(
            $this->createJpegWithOrientation(800, 400, 1),
            $manager,
            [new Orientation(), new Crop()],
        );

        $this->assertSame([800, 400], [$image->width(), $image->height()]);
    }

    public function testRunShrinksExifRotatedJpegOnLoad(): void
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('The exif extension is required.');
        }

        $manager = ImageManager::usingDriver(GdDriver::class);
        $image = (new Decoder(['w' => '100']))->run(
            $this->createJpegWithOrientation(800, 400, 6),
            $manager,
            [new Orientation(), new Crop(), new Size()],
        );

        // shrunk by 2 to keep twice the requested size, then oriented: the red top-left quadrant ends up top-right
        $this->assertSame([200, 400], [$image->width(), $image->height()]);
        $this->assertTrue($this->isRedAt($image, 150, 50));
        $this->assertFalse($this->isRedAt($image, 50, 50));
        $this->assertTrue($manager->driver->config()->autoOrientation);
    }

    public function testRunShrinksJpegOnLoadWithImagick(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('The imagick extension is required.');
        }

        $image = (new Decoder(['w' => '100']))->run(
            $this->createJpegWithOrientation(800, 400, 1),
            ImageManager::usingDriver(ImagickDriver::class),
            [new Size()],
        );

        $this->assertSame([400, 200], [$image->width(), $image->height()]);
        $this->assertTrue($this->isRedAt($image, 50, 50));
    }

    private function isRedAt(ImageInterface $image, int $x, int $y): bool
    {
        $color = $image->colorAt($x, $y)->toHex();

        return hexdec(substr($color, 0, 2)) > 200 && hexdec(substr($color, 4, 2)) < 50;
    }
}
