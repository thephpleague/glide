<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

use Intervention\Image\Config;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrientationTest extends TestCase
{
    private $manipulator;

    public function setUp(): void
    {
        $this->manipulator = new Orientation();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testCreateInstance()
    {
        $this->assertInstanceOf(Orientation::class, $this->manipulator);
    }

    public function testRun()
    {
        $image = $this->mockImage(autoOrientation: true, exifOrientation: 6);
        $image->shouldNotReceive('orient');
        $image->shouldReceive('rotate')->andReturn($image)->with('90')->once();

        $this->assertSame(
            $image,
            $this->manipulator->setParams(['or' => 'auto'])->run($image),
        );

        $this->assertInstanceOf(
            ImageInterface::class,
            $this->manipulator->setParams(['or' => '90'])->run($image),
        );
    }

    public function testRunAutoOrientsWhenDecoderDidNot()
    {
        $oriented = \Mockery::mock(ImageInterface::class);
        $image = $this->mockImage(autoOrientation: false, exifOrientation: 6);
        $image->shouldReceive('orient')->andReturn($oriented)->once();

        $this->assertSame($oriented, $this->manipulator->setParams(['or' => 'auto'])->run($image));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function alignedExifOrientationProvider(): iterable
    {
        yield 'missing' => [null];
        yield 'top-left' => [1];
    }

    #[DataProvider('alignedExifOrientationProvider')]
    public function testRunSkipsAlignedImages(mixed $exifOrientation)
    {
        $image = $this->mockImage(autoOrientation: false, exifOrientation: $exifOrientation);
        $image->shouldNotReceive('orient');

        $this->assertSame($image, $this->manipulator->setParams(['or' => 'auto'])->run($image));
    }

    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function driverProvider(): iterable
    {
        $drivers = [
            'gd' => [GdDriver::class, 'gd'],
            'imagick' => [ImagickDriver::class, 'imagick'],
            // The vips driver lives in the optional intervention/image-driver-vips package (installed in CI)
            'vips' => ['Intervention\\Image\\Drivers\\Vips\\Driver', 'ffi'],
        ];

        foreach ($drivers as $name => [$driver, $extension]) {
            yield $name . ', decoder orients' => [$driver, $extension, true];
            yield $name . ', decoder keeps orientation' => [$driver, $extension, false];
        }
    }

    #[DataProvider('driverProvider')]
    public function testRunAutoOrientsDecodedJpeg(string $driver, string $extension, bool $autoOrientation)
    {
        if (!class_exists($driver) || !extension_loaded($extension) || !function_exists('exif_read_data')) {
            $this->markTestSkipped(sprintf('The %s driver and the %s and exif extensions are required.', $driver, $extension));
        }

        // libvips < 8.13 stores vips-sequential as a VipsArea, which the vips driver cannot read when orienting
        if ($extension === 'ffi' && version_compare(\Jcupitt\Vips\Config::version(), '8.13', '<')) {
            $this->markTestSkipped('The vips driver requires libvips 8.13 or later to orient images.');
        }

        $manager = ImageManager::usingDriver($driver, autoOrientation: $autoOrientation);

        // EXIF orientation 6: the stored 80x40 image is displayed rotated 90° clockwise (40x80).
        $image = $manager->decodeBinary($this->createJpegWithOrientation(80, 40, 6));
        $image = $this->manipulator->setParams(['or' => 'auto'])->run($image);

        $this->assertSame([40, 80], [$image->width(), $image->height()]);

        $output = imagecreatefromstring((string) $image->encodeUsingFormat(Format::PNG));
        $this->assertNotFalse($output);
        $this->assertTrue($this->isRed($output, 30, 10), 'The red top-left quadrant should end up top-right.');
        $this->assertFalse($this->isRed($output, 10, 10));
        $this->assertFalse($this->isRed($output, 30, 70));
    }

    public function testGetOrientation()
    {
        $this->assertSame('auto', $this->manipulator->setParams(['or' => 'auto'])->getOrientation());
        $this->assertSame('0', $this->manipulator->setParams(['or' => '0'])->getOrientation());
        $this->assertSame('90', $this->manipulator->setParams(['or' => '90'])->getOrientation());
        $this->assertSame('180', $this->manipulator->setParams(['or' => '180'])->getOrientation());
        $this->assertSame('270', $this->manipulator->setParams(['or' => '270'])->getOrientation());
        $this->assertSame('auto', $this->manipulator->setParams(['or' => null])->getOrientation());
        $this->assertSame('auto', $this->manipulator->setParams(['or' => '1'])->getOrientation());
        $this->assertSame('auto', $this->manipulator->setParams(['or' => '45'])->getOrientation());
    }

    /**
     * @return ImageInterface&MockInterface
     */
    private function mockImage(bool $autoOrientation, mixed $exifOrientation): ImageInterface
    {
        $driver = \Mockery::mock(DriverInterface::class);
        $driver->shouldReceive('config')->andReturn(new Config(autoOrientation: $autoOrientation));

        $image = \Mockery::mock(ImageInterface::class);
        $image->shouldReceive('driver')->andReturn($driver);
        $image->shouldReceive('exif')->with('IFD0.Orientation')->andReturn($exifOrientation);

        return $image;
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
