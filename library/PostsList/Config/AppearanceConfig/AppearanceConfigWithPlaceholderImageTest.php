<?php

namespace Municipio\PostsList\Config\AppearanceConfig;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetThemeMod;

class AppearanceConfigWithPlaceholderImageTest extends TestCase
{
    #[TestDox('shouldDisplayPlaceholderImage() returns the provided value for displaying placeholder image')]
    public function testShouldDisplayPlaceholderImage(): void
    {
        $wpService = new class implements GetThemeMod {
            public function getThemeMod(string $name, mixed $defaultValue = false): mixed
            {
                return null;
            }
        };

        $appearanceConfigWithoutPlaceholder = new AppearanceConfigWithPlaceholderImage(false, $wpService);
        $this->assertFalse($appearanceConfigWithoutPlaceholder->shouldDisplayPlaceholderImage());
        $appearanceConfigWithPlaceholder = new AppearanceConfigWithPlaceholderImage(true, $wpService, $appearanceConfigWithoutPlaceholder);
        $this->assertTrue($appearanceConfigWithPlaceholder->shouldDisplayPlaceholderImage());
    }

    #[TestDox('getPlaceholderImageUrl() returns the theme mod value for logotype_emblem')]
    public function testGetPlaceholderImageUrl(): void
    {
        $wpService = new class implements GetThemeMod {
            public function getThemeMod(string $name, mixed $defaultValue = false): mixed
            {
                return $name === 'logotype_emblem' ? 'test-url' : $defaultValue;
            }
        };

        $appearanceConfig = new AppearanceConfigWithPlaceholderImage(true, $wpService);
        $this->assertEquals('test-url', $appearanceConfig->getPlaceholderImageUrl());
    }
}
