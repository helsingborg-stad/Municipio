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
}
