<?php

namespace Municipio\PostsList\Config\PostsListAppearanceConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DefaultPostsListAppearanceConfigTest extends TestCase
{
    #[TestDox('default design is CARD')]
    public function testDefaultDesignIsCard(): void
    {
        $config = new DefaultPostsListAppearanceConfig();
        $this->assertSame(PostDesign::CARD, $config->getDesign());
    }

    #[TestDox('default reading time display is false')]
    public function testDefaultReadingTimeDisplayIsFalse(): void
    {
        $config = new DefaultPostsListAppearanceConfig();
        $this->assertFalse($config->shouldDisplayReadingTime());
    }

    #[TestDox('default placeholder image display is false')]
    public function testDefaultPlaceholderImageDisplayIsFalse(): void
    {
        $config = new DefaultPostsListAppearanceConfig();
        $this->assertFalse($config->shouldDisplayPlaceholderImage());
    }
}
