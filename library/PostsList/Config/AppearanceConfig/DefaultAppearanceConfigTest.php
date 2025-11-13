<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DefaultAppearanceConfigTest extends TestCase
{
    #[TestDox('default design is CARD')]
    public function testDefaultDesignIsCard(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertSame(PostDesign::CARD, $config->getDesign());
    }

    #[TestDox('default reading time display is false')]
    public function testDefaultReadingTimeDisplayIsFalse(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertFalse($config->shouldDisplayReadingTime());
    }

    #[TestDox('default placeholder image display is false')]
    public function testDefaultPlaceholderImageDisplayIsFalse(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertFalse($config->shouldDisplayPlaceholderImage());
    }

    #[TestDox('default featured image display is false')]
    public function testDefaultFeaturedImageDisplayIsFalse(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertFalse($config->shouldDisplayFeaturedImage());
    }

    #[TestDox('default image ratio is SQUARE')]
    public function testDefaultImageRatioIsSquare(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertSame(ImageRatio::SQUARE, $config->getImageRatio());
    }

    #[TestDox('default number of columns is 1')]
    public function testDefaultNumberOfColumnsIsOne(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertSame(1, $config->getNumberOfColumns());
    }

    #[TestDox('default post properties to display is empty array')]
    public function testDefaultPostPropertiesToDisplayIsEmptyArray(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertSame([], $config->getPostPropertiesToDisplay());
    }
}
