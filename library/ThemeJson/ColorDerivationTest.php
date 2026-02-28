<?php

namespace Municipio\ThemeJson;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ColorDerivationTest extends TestCase
{
    #[TestDox('darken reduces brightness of a color')]
    public function testDarkenReducesBrightness(): void
    {
        $result = ColorDerivation::darken('#ffffff', 50);
        $this->assertEquals('#808080', $result);
    }

    #[TestDox('lighten increases brightness of a color')]
    public function testLightenIncreasesBrightness(): void
    {
        $result = ColorDerivation::lighten('#000000', 50);
        $this->assertEquals('#808080', $result);
    }

    #[TestDox('isValidHexColor returns true for valid hex colors')]
    public function testIsValidHexColorReturnsTrueForValidColors(): void
    {
        $this->assertTrue(ColorDerivation::isValidHexColor('#ae0b05'));
        $this->assertTrue(ColorDerivation::isValidHexColor('ae0b05'));
        $this->assertTrue(ColorDerivation::isValidHexColor('#fff'));
        $this->assertTrue(ColorDerivation::isValidHexColor('fff'));
    }

    #[TestDox('isValidHexColor returns false for invalid colors')]
    public function testIsValidHexColorReturnsFalseForInvalidColors(): void
    {
        $this->assertFalse(ColorDerivation::isValidHexColor('rgb(0,0,0)'));
        $this->assertFalse(ColorDerivation::isValidHexColor('rgba(0,0,0,0.5)'));
        $this->assertFalse(ColorDerivation::isValidHexColor('invalid'));
        $this->assertFalse(ColorDerivation::isValidHexColor('#gggggg'));
    }

    #[TestDox('normalizeToHex returns hex from hex input')]
    public function testNormalizeToHexReturnsHexFromHexInput(): void
    {
        $this->assertEquals('#ae0b05', ColorDerivation::normalizeToHex('#ae0b05'));
        $this->assertEquals('#ae0b05', ColorDerivation::normalizeToHex('ae0b05'));
        $this->assertEquals('#fff', ColorDerivation::normalizeToHex('fff'));
    }

    #[TestDox('normalizeToHex converts rgb to hex')]
    public function testNormalizeToHexConvertsRgbToHex(): void
    {
        $this->assertEquals('#ff0000', ColorDerivation::normalizeToHex('rgb(255, 0, 0)'));
        $this->assertEquals('#000000', ColorDerivation::normalizeToHex('rgb(0,0,0)'));
    }

    #[TestDox('normalizeToHex converts rgba to hex')]
    public function testNormalizeToHexConvertsRgbaToHex(): void
    {
        $this->assertEquals('#ff0000', ColorDerivation::normalizeToHex('rgba(255, 0, 0, 0.5)'));
    }

    #[TestDox('normalizeToHex returns null for invalid input')]
    public function testNormalizeToHexReturnsNullForInvalidInput(): void
    {
        $this->assertNull(ColorDerivation::normalizeToHex('invalid'));
        $this->assertNull(ColorDerivation::normalizeToHex('hsl(0, 100%, 50%)'));
    }

    #[TestDox('deriveVariants returns dark, light, and contrasting colors')]
    public function testDeriveVariantsReturnsAllVariants(): void
    {
        $variants = ColorDerivation::deriveVariants('#ae0b05');

        $this->assertArrayHasKey('dark', $variants);
        $this->assertArrayHasKey('light', $variants);
        $this->assertArrayHasKey('contrasting', $variants);

        // Dark should be darker than original
        $this->assertNotEquals('#ae0b05', $variants['dark']);
        // Light should be lighter than original
        $this->assertNotEquals('#ae0b05', $variants['light']);
        // Contrasting should be either very light or very dark
        $this->assertNotEmpty($variants['contrasting']);
    }
}
