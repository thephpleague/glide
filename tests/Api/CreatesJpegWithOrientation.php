<?php

declare(strict_types=1);

namespace League\Glide\Api;

trait CreatesJpegWithOrientation
{
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
