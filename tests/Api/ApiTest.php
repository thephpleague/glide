<?php

declare(strict_types=1);

namespace League\Glide\Api;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Exceptions\ImageDecoderException;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use League\Glide\Manipulators\Crop;
use League\Glide\Manipulators\ManipulatorInterface;
use League\Glide\Manipulators\Orientation;
use League\Glide\Manipulators\Size;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private Api $api;

    public function setUp(): void
    {
        $this->api = new Api(ImageManager::usingDriver(Driver::class), []);
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Api::class, $this->api);
    }

    public function testSetImageManager(): void
    {
        $this->api->setImageManager(ImageManager::usingDriver(Driver::class));
        $this->assertInstanceOf(ImageManager::class, $this->api->getImageManager());
    }

    public function testGetImageManager(): void
    {
        $this->assertInstanceOf(ImageManager::class, $this->api->getImageManager());
    }

    public function testSetManipulators(): void
    {
        $this->api->setManipulators([\Mockery::mock(ManipulatorInterface::class)]);
        $manipulators = $this->api->getManipulators();
        $this->assertInstanceOf(ManipulatorInterface::class, $manipulators[0]);
    }

    public function testSetInvalidManipulator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not a valid manipulator.');

        $this->api->setManipulators([new \stdClass()]);
    }

    public function testGetManipulators(): void
    {
        $this->assertEquals([], $this->api->getManipulators());
    }

    public function testGetApiParams(): void
    {
        $manipulator1 = \Mockery::mock(ManipulatorInterface::class, function ($mock) {
            $mock->shouldReceive('getApiParams')->andReturn(['foo', 'bar']);
        });
        $manipulator2 = \Mockery::mock(ManipulatorInterface::class, function ($mock) {
            $mock->shouldReceive('getApiParams')->andReturn(['foo', 'baz']);
        });

        $api = new Api(ImageManager::usingDriver(Driver::class), [$manipulator1, $manipulator2]);
        $this->assertEquals(array_merge(Api::GLOBAL_API_PARAMS, ['foo', 'bar', 'baz']), $api->getApiParams());
    }

    public function testRun(): void
    {
        $image = \Mockery::mock(ImageInterface::class, function ($mock) {
            $mock->shouldReceive('origin')->andReturn(\Mockery::mock('\Intervention\Image\Origin', function ($mock) {
                $mock->shouldReceive('mediaType')->andReturn('image/png');
            }));

            $mock->shouldReceive('encodeUsingFileExtension')->with('png')->andReturn(\Mockery::mock(EncodedImageInterface::class, function ($mock) {
                $mock->shouldReceive('toString')->andReturn('encoded');
            }));
        });

        $manager = ImageManager::usingDriver(Driver::class);

        $manipulator = \Mockery::mock(ManipulatorInterface::class, function ($mock) use ($image) {
            $mock->shouldReceive('setParams')->with([]);
            $mock->shouldReceive('run')->andReturn($image);
            $mock->shouldReceive('getApiParams')->andReturn(['p', 'q', 'fm', 's']);
        });

        $api = new Api($manager, [$manipulator]);

        $this->assertEquals('encoded', $api->run(
            (string) file_get_contents(dirname(__FILE__, 2) . '/files/red-pixel.png'),
            [],
        ));
    }

    /**
     * @return iterable<string, array{class-string<DriverInterface>, string}>
     */
    public static function shrinkOnLoadProvider(): iterable
    {
        yield 'gd' => [Driver::class, 'gd'];
        yield 'imagick' => [ImagickDriver::class, 'imagick'];
    }

    /**
     * @param class-string<DriverInterface> $driver
     */
    #[DataProvider('shrinkOnLoadProvider')]
    public function testRunShrinksExifRotatedJpegOnLoad(string $driver, string $extension): void
    {
        if (!extension_loaded($extension) || !function_exists('exif_read_data')) {
            $this->markTestSkipped(sprintf('The %s and exif extensions are required.', $extension));
        }

        $manager = ImageManager::usingDriver($driver);
        $api = new Api($manager, [new Orientation(), new Crop(), new Size()]);

        // EXIF orientation 6: the stored 800x400 image is displayed rotated 90° clockwise (400x800).
        $output = imagecreatefromstring($api->run($this->createJpegWithOrientation(800, 400, 6), ['w' => '100']));

        $this->assertNotFalse($output);
        $this->assertSame([100, 200], [imagesx($output), imagesy($output)]);
        $this->assertTrue($this->isRed($output, 75, 25), 'The red top-left quadrant should end up top-right.');
        $this->assertFalse($this->isRed($output, 25, 25));
        $this->assertFalse($this->isRed($output, 75, 175));
        $this->assertTrue($manager->driver->config()->autoOrientation);
    }

    /**
     * @param class-string<DriverInterface> $driver
     */
    #[DataProvider('shrinkOnLoadProvider')]
    public function testRunOrientsWhenAutoOrientationIsDisabled(string $driver, string $extension): void
    {
        if (!extension_loaded($extension) || !function_exists('exif_read_data')) {
            $this->markTestSkipped(sprintf('The %s and exif extensions are required.', $extension));
        }

        $api = new Api(ImageManager::usingDriver($driver, autoOrientation: false), [new Orientation(), new Crop(), new Size()]);

        // The decoder leaves the image as stored: the Orientation manipulator (or=auto) has to rotate it.
        $output = imagecreatefromstring($api->run($this->createJpegWithOrientation(800, 400, 6), ['w' => '100']));

        $this->assertNotFalse($output);
        $this->assertSame([100, 200], [imagesx($output), imagesy($output)]);
        $this->assertTrue($this->isRed($output, 75, 25), 'The red top-left quadrant should end up top-right.');
        $this->assertFalse($this->isRed($output, 25, 25));
    }

    /**
     * @param class-string<DriverInterface> $driver
     */
    #[DataProvider('shrinkOnLoadProvider')]
    public function testRunKeepsFullSizeSourceWhenCropping(string $driver, string $extension): void
    {
        if (!extension_loaded($extension) || !function_exists('exif_read_data')) {
            $this->markTestSkipped(sprintf('The %s and exif extensions are required.', $extension));
        }

        $api = new Api(ImageManager::usingDriver($driver), [new Orientation(), new Crop(), new Size()]);

        // Crop coordinates are relative to the full-size, oriented source: the top-right red quadrant.
        $output = imagecreatefromstring($api->run($this->createJpegWithOrientation(800, 400, 6), ['crop' => '200,400,200,0', 'w' => '50']));

        $this->assertNotFalse($output);
        $this->assertSame([50, 100], [imagesx($output), imagesy($output)]);
        $this->assertTrue($this->isRed($output, 25, 50));
    }

    /**
     * @return iterable<string, array{int, int, int, list<ManipulatorInterface>, array{int, int}, array{int, int}}>
     */
    public static function regularDecodeProvider(): iterable
    {
        yield 'upright image' => [800, 400, 1, [new Orientation(), new Crop(), new Size()], [100, 50], [10, 10]];
        yield 'no exact shrink factor' => [801, 401, 6, [new Orientation(), new Crop(), new Size()], [100, 200], [90, 10]];
        yield 'no resize manipulator' => [800, 400, 6, [new Orientation(), new Crop()], [400, 800], [350, 50]];
    }

    /**
     * @param list<ManipulatorInterface> $manipulators
     * @param array{int, int}            $size
     * @param array{int, int}            $redPixel
     */
    #[DataProvider('regularDecodeProvider')]
    public function testRunFallsBackToRegularDecoding(int $width, int $height, int $orientation, array $manipulators, array $size, array $redPixel): void
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('The exif extension is required.');
        }

        $api = new Api(ImageManager::usingDriver(Driver::class), $manipulators);
        $output = imagecreatefromstring($api->run($this->createJpegWithOrientation($width, $height, $orientation), ['w' => '100']));

        $this->assertNotFalse($output);
        $this->assertSame($size, [imagesx($output), imagesy($output)]);
        $this->assertTrue($this->isRed($output, ...$redPixel));
    }

    public function testRunReportsUndecodableJpegWithImagick(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('The imagick extension is required.');
        }

        // The header is valid, but the image data is missing.
        $jpeg = $this->createJpegWithOrientation(800, 400, 1);
        $api = new Api(ImageManager::usingDriver(ImagickDriver::class), [new Size()]);

        $this->expectException(ImageDecoderException::class);

        $api->run(substr($jpeg, 0, (int) strpos($jpeg, "\xFF\xDA")), ['w' => '100']);
    }

    /**
     * Create a JPEG with a red top-left quadrant on a blue background, tagged with the given EXIF orientation.
     */
    private function createJpegWithOrientation(int $width, int $height, int $orientation): string
    {
        $gd = imagecreatetruecolor($width, $height);
        imagefill($gd, 0, 0, (int) imagecolorallocate($gd, 0, 0, 255));
        imagefilledrectangle($gd, 0, 0, intdiv($width, 2) - 1, intdiv($height, 2) - 1, (int) imagecolorallocate($gd, 255, 0, 0));

        ob_start();
        imagejpeg($gd, null, 95);
        $jpeg = (string) ob_get_clean();

        $tiff = "II*\0" . pack('V', 8) . pack('v', 1) . pack('vvVvv', 0x0112, 3, 1, $orientation, 0) . pack('V', 0);
        $app1 = "Exif\0\0" . $tiff;

        return substr($jpeg, 0, 2) . "\xFF\xE1" . pack('n', strlen($app1) + 2) . $app1 . substr($jpeg, 2);
    }

    private function isRed(\GdImage $image, int $x, int $y): bool
    {
        $rgb = imagecolorat($image, $x, $y);

        return ($rgb >> 16 & 0xFF) > 200 && ($rgb & 0xFF) < 50;
    }
}
