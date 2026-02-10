<?php

namespace Municipio\ThemeJson;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ThemeJsonGeneratorTest extends TestCase
{
    #[TestDox('constructor registers the wp_theme_json_data_theme filter')]
    public function testConstructorRegistersFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        new ThemeJsonGenerator($wpService);

        $this->assertCount(1, $wpService->methodCalls['addFilter'] ?? []);
        $this->assertEquals('wp_theme_json_data_theme', $wpService->methodCalls['addFilter'][0][0]);
    }

    #[TestDox('mergeCustomizerColors returns original theme json when no customizer values exist')]
    public function testMergeCustomizerColorsReturnsOriginalWhenNoValues(): void
    {
        $wpService = new FakeWpService([
            'addFilter'   => true,
            'getThemeMod' => false, // No theme mods set
        ]);

        $generator = new ThemeJsonGenerator($wpService);

        // Create a mock WP_Theme_JSON_Data
        $themeJsonData = $this->createMock(\WP_Theme_JSON_Data::class);
        $themeJsonData->expects($this->never())->method('update_with');

        $result = $generator->mergeCustomizerColors($themeJsonData);

        $this->assertSame($themeJsonData, $result);
    }
}
