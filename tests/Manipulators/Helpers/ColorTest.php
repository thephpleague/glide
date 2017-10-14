<?php

namespace League\Glide\Manipulators\Helpers;

use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testThreeDigitColorCode()
    {
        $color = new Color('000');

        $this->assertSame('rgba(0, 0, 0, 1)', $color->formatted());
    }

    public function testFourDigitColorCode()
    {
        $color = new Color('5000');

        $this->assertSame('rgba(0, 0, 0, 0.5)', $color->formatted());
    }

    public function testSixDigitColorCode()
    {
        $color = new Color('000000');

        $this->assertSame('rgba(0, 0, 0, 1)', $color->formatted());
    }

    public function testEightDigitColorCode()
    {
        $color = new Color('50000000');

        $this->assertSame('rgba(0, 0, 0, 0.5)', $color->formatted());
    }

    public function testNamedColorCode()
    {
        $color = new Color('black');

        $this->assertSame('rgba(0, 0, 0, 1)', $color->formatted());
    }

    public function testUnknownColor()
    {
        $color = new Color('unknown');

        $this->assertSame('rgba(255, 255, 255, 0)', $color->formatted());
    }
}
