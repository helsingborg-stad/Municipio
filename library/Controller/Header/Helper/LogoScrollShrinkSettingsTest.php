<?php

declare(strict_types=1);

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Header;
use Municipio\Controller\Header\MenuItemFactory;
use PHPUnit\Framework\TestCase;

class LogoScrollShrinkSettingsTest extends TestCase
{
    public function testResolvesEnabledAndValuesForValidLowerLogotypeSetup(): void
    {
        $customizer = (object) [
            'headerSticky' => 'sticky',
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 0.4,
            'headerLogoScrollAspectRatio' => 1.8,
            'headerSortableSectionMainLower' => json_encode(['logotype', 'primary']),
            'headerSortableSectionMainLowerResponsive' => json_encode([]),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                    'primary' => ['align' => 'center', 'margin' => 'none'],
                ],
                'header_sortable_section_main_lower_responsive' => [],
            ]),
        ];

        $settings = new LogoScrollShrinkSettings($customizer);
        $resolved = $settings->resolve($this->createLowerHeader($customizer), false);

        static::assertTrue($resolved['enabled']);
        static::assertSame(0.4, $resolved['overlapMultiplier']);
        static::assertSame(1.8, $resolved['aspectRatio']);
    }

    public function testCanEnableWithoutStickyWhenOldConditionsAreMet(): void
    {
        $customizer = (object) [
            'headerSticky' => '',
            'headerLogoScrollShrink' => true,
            'headerSortableSectionMainLower' => json_encode(['logotype']),
            'headerSortableSectionMainLowerResponsive' => json_encode([]),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                ],
                'header_sortable_section_main_lower_responsive' => [],
            ]),
        ];

        $settings = new LogoScrollShrinkSettings($customizer);
        $resolved = $settings->resolve($this->createLowerHeader($customizer), false);

        static::assertTrue($resolved['enabled']);
    }

    public function testDisablesAspectRatioInCustomizePreview(): void
    {
        $customizer = (object) [
            'headerSticky' => 'sticky',
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 0.5,
            'headerLogoScrollAspectRatio' => 2,
            'headerSortableSectionMainLower' => json_encode(['logotype']),
            'headerSortableSectionMainLowerResponsive' => json_encode([]),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                ],
                'header_sortable_section_main_lower_responsive' => [],
            ]),
        ];

        $settings = new LogoScrollShrinkSettings($customizer);
        $resolved = $settings->resolve($this->createLowerHeader($customizer), true);

        static::assertNull($resolved['aspectRatio']);
    }

    public function testFallsBackToDefaultsAndDisabledWhenConditionsDoNotMatch(): void
    {
        $customizer = (object) [
            'headerSticky' => '',
            'headerLogoScrollShrink' => false,
            'headerLogoOverlapMultiplier' => 2,
            'headerLogoScrollAspectRatio' => -1,
            'headerSortableSectionMainLower' => json_encode(['primary']),
            'headerSortableSectionMainLowerResponsive' => json_encode([]),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'primary' => ['align' => 'left', 'margin' => 'none'],
                ],
                'header_sortable_section_main_lower_responsive' => [],
            ]),
        ];

        $settings = new LogoScrollShrinkSettings($customizer);
        $resolved = $settings->resolve($this->createLowerHeader($customizer), false);

        static::assertFalse($resolved['enabled']);
        static::assertSame(0.25, $resolved['overlapMultiplier']);
        static::assertNull($resolved['aspectRatio']);
    }

    private function createLowerHeader(object $customizer): Header
    {
        $extractMenuItems = new ExtractMenuItems($customizer);

        return new Header(
            Enums::HEADER_KEY::LOWER->value,
            new MenuItemFactory(new MenuItemClasses()),
            $extractMenuItems,
            new HeaderClasses(),
            new HeaderAttributes(),
            new StickyResolver($extractMenuItems, $customizer),
        );
    }
}