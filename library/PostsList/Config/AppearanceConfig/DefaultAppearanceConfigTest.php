<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
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

    #[TestDox('getDateSource returns post_date by default')]
    public function testGetDateSourceReturnsPostDateByDefault(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertSame('post_date', $config->getDateSource());
    }

    #[TestDox('getDateFormat returns DATE_TIME by default')]
    public function testGetDateFormatReturnsDateByDefault(): void
    {
        $config = new DefaultAppearanceConfig();
        $this->assertSame(DateFormat::DATE_TIME, $config->getDateFormat());
    }
}
