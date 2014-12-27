<?php

namespace Glide\Manipulators;

use Glide\Interfaces\Manipulator;
use Glide\Request;
use Intervention\Image\Image;

class Adjustments implements Manipulator
{
    public function validate(Request $request, Image $image)
    {
        return array_merge(
            $this->validateBrightness($request->bri),
            $this->validateContrast($request->con),
            $this->validateGamma($request->gam),
            $this->validateSharpen($request->sharp)
        );
    }

    public function validateBrightness($brightness)
    {
        if (is_null($brightness)) {
            return [];
        }

        if (!preg_match('/^-*[0-9]+$/', $brightness)) {
            return ['bri' => 'Brightness must be a valid number.'];
        }

        if ($brightness < -100 or $brightness > 100) {
            return ['bri' => 'Brightness must be between `-100` and `100`.'];
        }

        return [];
    }

    public function validateContrast($contrast)
    {
        if (is_null($contrast)) {
            return [];
        }

        if (!preg_match('/^-*[0-9]+$/', $contrast)) {
            return ['con' => 'Contrast must be a valid number.'];
        }

        if ($contrast < -100 or $contrast > 100) {
            return ['con' => 'Contrast must be between `-100` and `100`.'];
        }

        return [];
    }

    public function validateGamma($gamma)
    {
        if (is_null($gamma)) {
            return [];
        }

        if (!preg_match('/^-*[0-9]+$/', $gamma)) {
            return ['gam' => 'Gamma must be a valid number.'];
        }

        return [];
    }

    public function validateSharpen($sharpen)
    {
        if (is_null($sharpen)) {
            return [];
        }

        if (!ctype_digit($sharpen)) {
            return ['sharp' => 'Sharpen must be a valid number.'];
        }

        if ($sharpen < 0 or $sharpen > 100) {
            return ['sharp' => 'Sharpen must be between `0` and `100`.'];
        }

        return [];
    }

    public function run(Request $request, Image $image)
    {
        if ($request->bri) {
            $image->brightness($request->bri);
        }

        if ($request->con) {
            $image->contrast($request->con);
        }

        if ($request->gam) {
            $image->gamma($request->gam);
        }

        if ($request->sharp) {
            $image->sharpen($request->sharp);
        }
    }
}
