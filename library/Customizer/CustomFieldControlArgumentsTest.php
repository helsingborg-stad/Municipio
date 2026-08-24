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
}
