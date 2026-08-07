<?php

namespace Municipio\Controller\Header;

use PHPUnit\Framework\TestCase;

class FlexibleTest extends TestCase
{
    public function testGetHeaderDataUsesResponsiveOrderWhenResponsiveSectionsHaveItems(): void
    {
        $controller = new Flexible((object) [
            'headerSortableHiddenStorage' => $this->getHiddenStorage(),
            'headerSortableSectionMainUpper' => ['menu', 'search-modal'],
            'headerSortableSectionMainLower' => [],
            'headerSortableSectionMainUpperResponsive' => ['search-modal', 'menu'],
            'headerSortableSectionMainLowerResponsive' => [],
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertSame(['u-order--0', 'u-order--1@md', 'u-display--flex'], $headerData['upperItems']['right']['search-modal']);
        $this->assertSame(['u-order--1', 'u-order--0@md', 'u-display--flex'], $headerData['upperItems']['right']['menu']);
    }

    public function testGetHeaderDataRespectsExplicitlyEmptyResponsiveSections(): void
    {
        $controller = new Flexible((object) [
            'headerSortableHiddenStorage' => $this->getHiddenStorage(),
            'headerSortableSectionMainUpper' => ['menu', 'search-modal'],
            'headerSortableSectionMainLower' => [],
            'headerSortableSectionMainUpperResponsive' => [],
            'headerSortableSectionMainLowerResponsive' => [],
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertContains('u-display--none', $headerData['upperItems']['right']['menu']);
        $this->assertContains('u-display--none', $headerData['upperItems']['right']['search-modal']);
    }

    public function testGetHeaderDataFallsBackToDesktopOrderWhenResponsiveSectionsAreMissing(): void
    {
        $controller = new Flexible((object) [
            'headerSortableHiddenStorage' => $this->getHiddenStorage(),
            'headerSortableSectionMainUpper' => ['menu', 'search-modal'],
            'headerSortableSectionMainLower' => [],
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertSame(['u-order--0', 'u-order--0@md', 'u-display--flex'], $headerData['upperItems']['right']['menu']);
        $this->assertSame(['u-order--1', 'u-order--1@md', 'u-display--flex'], $headerData['upperItems']['right']['search-modal']);
    }

    public function testGetHeaderDataDoesNotDuplicateItemsWhenAlignmentAndMarginUseDifferentSides(): void
    {
        $controller = new Flexible((object) [
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'right',
                    ],
                ],
            ]),
            'headerSortableSectionMainUpper' => ['logotype'],
            'headerSortableSectionMainLower' => [],
            'headerSortableSectionMainUpperResponsive' => [],
            'headerSortableSectionMainLowerResponsive' => [],
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertContains('u-margin__right--2', $headerData['upperItems']['left']['logotype']);
        $this->assertArrayNotHasKey('right', $headerData['upperItems']);
    }

    public function testGetHeaderDataEnablesLogoScrollShrinkWhenLogotypeIsInLowerLeftAndSettingIsEnabled(): void
    {
        $controller = new Flexible((object) [
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 0.5,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainUpper' => ['primary'],
            'headerSortableSectionMainLower' => ['logotype'],
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertTrue($headerData['logoScrollShrinkEnabled']);
        $this->assertSame(0.5, $headerData['logoScrollShrinkOverlapMultiplier']);
    }

    public function testGetHeaderDataDisablesLogoScrollShrinkWhenLogotypeIsNotInLowerLeft(): void
    {
        $controller = new Flexible((object) [
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
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertFalse($headerData['logoScrollShrinkEnabled']);
    }

    public function testGetHeaderDataFallsBackToDefaultLogoOverlapMultiplierWhenValueIsUnsupported(): void
    {
        $controller = new Flexible((object) [
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 1.5,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainUpper' => ['primary'],
            'headerSortableSectionMainLower' => ['logotype'],
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertSame(0.25, $headerData['logoScrollShrinkOverlapMultiplier']);
    }

    public function testGetHeaderDataKeepsAdjustableLogoOverlapMultiplierWhenValueIsValid(): void
    {
        $controller = new Flexible((object) [
            'headerLogoScrollShrink' => true,
            'headerLogoOverlapMultiplier' => 0.4,
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_lower' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                ],
            ]),
            'headerSortableSectionMainUpper' => ['primary'],
            'headerSortableSectionMainLower' => ['logotype'],
        ]);

        $headerData = $controller->getHeaderData();

        $this->assertSame(0.4, $headerData['logoScrollShrinkOverlapMultiplier']);
    }

    private function getHiddenStorage(): string
    {
        return json_encode([
            'header_sortable_section_main_upper' => [
                'menu' => [
                    'align' => 'right',
                    'margin' => 'none',
                ],
                'search-modal' => [
                    'align' => 'right',
                    'margin' => 'none',
                ],
            ],
        ]);
    }
}
