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

    public function testGetHeaderDataFallsBackToDesktopOrderWhenResponsiveSectionsAreEmpty(): void
    {
        $controller = new Flexible((object) [
            'headerSortableHiddenStorage' => $this->getHiddenStorage(),
            'headerSortableSectionMainUpper' => ['menu', 'search-modal'],
            'headerSortableSectionMainLower' => [],
            'headerSortableSectionMainUpperResponsive' => [],
            'headerSortableSectionMainLowerResponsive' => [],
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

        $this->assertSame(['u-order--0', 'u-order--0@md', 'u-display--flex', 'u-margin__right--2'], $headerData['upperItems']['left']['logotype']);
        $this->assertArrayNotHasKey('right', $headerData['upperItems']);
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
