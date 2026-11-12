<?php

namespace Municipio\Customizer;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class CustomFieldControlArgumentsTest extends TestCase
{
    #[TestDox('fromField maps include_reset to custom control input attributes')]
    public function testFromFieldMapsIncludeResetToCustomControlInputAttributes(): void
    {
        // Arrange
        $field = [
            'section' => 'hero_section',
            'settings' => 'hero_content_bg_color',
            'include_reset' => true,
        ];

        // Act
        $arguments = CustomFieldControlArguments::fromField($field);

        // Assert
        $this->assertArrayHasKey('input_attrs', $arguments);
        $this->assertTrue($arguments['input_attrs']['include_reset']);
    }

    #[TestDox('fromField falls back to includeReset for backwards compatibility')]
    public function testFromFieldFallsBackToIncludeResetForBackwardsCompatibility(): void
    {
        // Arrange
        $field = [
            'section' => 'hero_section',
            'settings' => 'hero_content_bg_color',
            'includeReset' => true,
        ];

        // Act
        $arguments = CustomFieldControlArguments::fromField($field);

        // Assert
        $this->assertArrayHasKey('input_attrs', $arguments);
        $this->assertTrue($arguments['input_attrs']['include_reset']);
    }

    #[TestDox('fromField maps reset_preview to custom control input attributes')]
    public function testFromFieldMapsResetPreviewToCustomControlInputAttributes(): void
    {
        // Arrange
        $field = [
            'section' => 'drawer_section',
            'settings' => 'drawer_current_badge_color',
            'reset_preview' => ['background' => '#e53935', 'contrast' => '#ffffff'],
        ];

        // Act
        $arguments = CustomFieldControlArguments::fromField($field);

        // Assert
        $this->assertSame(['background' => '#e53935', 'contrast' => '#ffffff'], $arguments['input_attrs']['reset_preview']);
    }
}
