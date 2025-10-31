<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AppearanceConfigWithPlaceholderImageTest extends TestCase
{
    #[TestDox('shouldDisplayPlaceholderImage() returns the provided value for displaying placeholder image')]
    public function testShouldDisplayPlaceholderImage(): void
    {
        $appearanceConfigWithoutPlaceholder = new AppearanceConfigWithPlaceholderImage(false);
        $this->assertFalse($appearanceConfigWithoutPlaceholder->shouldDisplayPlaceholderImage());
        $appearanceConfigWithPlaceholder = new AppearanceConfigWithPlaceholderImage(true, $appearanceConfigWithoutPlaceholder);
        $this->assertTrue($appearanceConfigWithPlaceholder->shouldDisplayPlaceholderImage());
    }
}
