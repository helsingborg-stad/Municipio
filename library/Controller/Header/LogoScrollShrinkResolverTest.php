<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\GetHiddenData;
use Municipio\Controller\Header\Helper\NormalizeOrderedItems;
use PHPUnit\Framework\TestCase;

class LogoScrollShrinkResolverTest extends TestCase
{
    public function testResolveEnablesLogoScrollShrinkWhenLogotypeIsInLowerLeftAndSettingIsEnabled(): void
    {
        $customizer = (object) [
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 0.5,
            'headerLogoScrollAspectRatio' => 3.7,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainLower' => ['logotype'],
        ];

        $resolver = $this->createResolver($customizer);
        $result = $resolver->resolve();

        $this->assertTrue($result['enabled']);
        $this->assertSame(0.5, $result['overlapMultiplier']);
        $this->assertSame(3.7, $result['aspectRatio']);
        $this->assertSame('--municipio-header-logo-overlap-multiplier: 0.5;', $result['style']);
        $this->assertSame(['style' => '--municipio-header-logo-overlap-multiplier: 0.5;'], $result['attributeList']);
    }

    public function testResolveOmitsAspectRatioInCustomizerPreview(): void
    {
        $customizer = (object) [
            'headerLogoScrollShrink' => true,
            'headerLogoScrollAspectRatio' => 3.7,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainLower' => ['logotype'],
        ];

        $resolver = $this->createResolver($customizer, true);
        $result = $resolver->resolve();

        $this->assertNull($result['aspectRatio']);
    }

    public function testResolveDisablesLogoScrollShrinkWhenLogotypeIsNotInLowerLeft(): void
    {
        $customizer = (object) [
            'headerLogoScrollShrink' => true,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainUpper' => ['logotype'],
            'headerSortableSectionMainLower' => ['primary'],
        ];

        $resolver = $this->createResolver($customizer);
        $result = $resolver->resolve();

        $this->assertFalse($result['enabled']);
        $this->assertNull($result['style']);
        $this->assertSame([], $result['attributeList']);
    }

    public function testResolveFallsBackToDefaultLogoOverlapMultiplierWhenValueIsUnsupported(): void
    {
        $customizer = (object) [
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 1.5,
            'headerLogoScrollAspectRatio' => 0,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainLower' => ['logotype'],
        ];

        $resolver = $this->createResolver($customizer);
        $result = $resolver->resolve();

        $this->assertSame(0.25, $result['overlapMultiplier']);
        $this->assertNull($result['aspectRatio']);
    }

    public function testResolveKeepsZeroLogoOverlapMultiplierWhenValueIsValid(): void
    {
        $customizer = (object) [
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 0,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainLower' => ['logotype'],
        ];

        $resolver = $this->createResolver($customizer);
        $result = $resolver->resolve();

        $this->assertSame(0.0, $result['overlapMultiplier']);
    }

    private function createResolver(object $customizer, bool $isCustomizePreview = false): LogoScrollShrinkResolver
    {
        return new LogoScrollShrinkResolver(
            $customizer,
            new GetHiddenData($customizer),
            new NormalizeOrderedItems(),
            $isCustomizePreview,
        );
    }
}