<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Size implements Manipulator
{
    private $maxImageSize;

    public function __construct($maxImageSize = null)
    {
        $this->maxImageSize = $maxImageSize;
    }

    public function validate(Request $request, Image $image)
    {
        $errors = array_merge(
            $this->validateWidth($request->w),
            $this->validateHeight($request->h),
            $this->validateFit($request->fit),
            $this->validateCropPosition($request->crop),
            $this->validateCropRectangle($request->rect, $image),
            $this->validateOrientation($request->or)
        );

        if ($errors) {
            return $errors;
        }

        return $this->validateSize($request, $image);
    }

    public function validateWidth($width)
    {
        if (is_null($width)) {
            return [];
        }

        if (!ctype_digit($width)) {
            return ['w' => 'Width must be a valid number.'];
        }

        if ($width <= 0) {
            return ['w' => 'Width must be greater than `0`.'];
        }

        return [];
    }

    public function validateHeight($height)
    {
        if (is_null($height)) {
            return [];
        }

        if (!ctype_digit($height)) {
            return ['h' => 'Height must be a valid number.'];
        }

        if ($height <= 0) {
            return ['h' => 'Height must be greater than `0`.'];
        }

        return [];
    }

    public function validateFit($fit)
    {
        if (is_null($fit)) {
            return [];
        }

        if (!in_array($fit, ['clip', 'scale', 'crop'])) {
            return ['fit' => 'Fit only accepts `clip`, `scale` or `crop`.'];
        }

        return [];
    }

    public function validateCropPosition($cropPosition)
    {
        if (is_null($cropPosition)) {
            return [];
        }

        if (!in_array($cropPosition, ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'])) {
            return ['crop' => 'The crop position parameter only accepts `top-left`, `top`, `top-right`, `left`, `center`, `right`, `bottom-left`, `bottom` or `bottom-right`.'];
        }

        return [];
    }

    public function validateCropRectangle($cropRectangle, Image $image)
    {
        if (is_null($cropRectangle)) {
            return [];
        }

        $coordinates = explode(',', $cropRectangle);

        if (count($coordinates) !== 4) {
            return ['rect' => 'Rectangle crop requires `width`, `height`, `x` and `y`.'];
        }

        $coordinates = [
            'width' => $coordinates[0],
            'height' => $coordinates[1],
            'x' => $coordinates[2],
            'y' => $coordinates[3],
        ];

        foreach ($coordinates as $name => $value) {
            if (!ctype_digit($value)) {
                return ['rect' => 'Rectangle crop ' . $name . ' must be a valid number.'];
            }

            if (in_array($name, ['width', 'height'])) {
                if ($value <= 0) {
                    return ['rect' => 'Rectangle crop ' . $name . ' must be greater than `0`.'];
                }
            }

            if (in_array($name, ['width', 'x'])) {
                if ($value > $image->width()) {
                    return ['rect' => 'Rectangle crop ' . $name . ' cannot be larger than the source image width.'];
                }
            }

            if (in_array($name, ['height', 'y'])) {
                if ($value > $image->height()) {
                    return ['rect' => 'Rectangle crop ' . $name . ' cannot be larger than the source image height.'];
                }
            }
        }

        return [];
    }

    public function validateOrientation($orientation)
    {
        if (is_null($orientation)) {
            return [];
        }

        if (!in_array($orientation, ['auto', '0', '90', '180', '270'])) {
            return ['or' => 'Orientation must be set to `auto`, `0`, `90`, `180` or `270`'];
        }

        return [];
    }

    public function validateSize(Request $request, Image $image)
    {
        if (is_null($this->maxImageSize)) {
            return [];
        }

        $sourceWidth = $image->width();
        $sourceHeight = $image->height();

        if ($request->rect) {
            $coordinates = explode(',', $request->rect);
            $sourceWidth = $coordinates[0];
            $sourceHeight = $coordinates[1];
        }

        if ($this->maxImageSize < $this->calculateTargetSize($sourceWidth, $sourceHeight, $request->w, $request->h)) {
            return ['size' => 'Image exceeds the maximum allowed size of `' . $this->maxImageSize . 'px`.'];
        }

        return [];
    }

    public function calculateTargetSize($sourceWidth, $sourceHeight, $targetWidth, $targetHeight)
    {
        if ($targetWidth and $targetHeight) {
            return $targetWidth * $targetWidth;
        }

        if ($targetWidth and !$targetHeight) {
            return $targetWidth * ($targetWidth / ($sourceWidth / $sourceHeight));
        }

        if (!$targetWidth and $targetHeight) {
            return ($targetHeight * ($sourceWidth / $sourceHeight)) * $targetHeight;
        }

        return $sourceWidth * $sourceHeight;
    }

    public function run(Request $request, Image $image)
    {
        if ($request->rect) {
            $this->runCropRectangle($image, $request->rect);
        }

        if ($request->w or $request->h) {
            $this->runResize($image, $request->fit, $request->w, $request->h, $request->crop);
        }

        $this->runOrientate($image, $request->or);
    }

    public function runCropRectangle(Image $image, $cropRectangle)
    {
        $coordinates = explode(',', $cropRectangle);
        $image->crop(
            (int) $coordinates[0],
            (int) $coordinates[1],
            (int) $coordinates[2],
            (int) $coordinates[3]
        );
    }

    public function runClipResize(Image $image, $width, $height)
    {
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    public function runResize(Image $image, $fit, $width, $height, $crop)
    {
        if (is_null($fit) or $fit === 'clip') {
            $this->runClipResize($image, $width, $height);
        }

        if ($fit === 'scale') {
            $this->runScaleResize($image, $width, $height);
        }

        if ($fit === 'crop') {
            $this->runCropResize($image, $width, $height, $crop);
        }
    }

    public function runScaleResize(Image $image, $width, $height)
    {
        $image->resize($width, $height);
    }

    public function runCropResize(Image $image, $width, $height, $crop)
    {
        $image->fit($width, $height, null, $crop ?: 'center');
    }

    public function runOrientate(Image $image, $orientation)
    {
        if (in_array($orientation, [null, 'auto'])) {
            $image->orientate();
        }

        if (in_array($orientation, ['90', '180', '270'])) {
            $image->rotate($orientation);
        }
    }
}
