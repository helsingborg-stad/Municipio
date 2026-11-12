<?php

declare(strict_types=1);

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\ExtractMenuItems;
use Municipio\Controller\Header\Helper\HeaderAttributes;
use Municipio\Controller\Header\Helper\HeaderClasses;
use Municipio\Controller\Header\Helper\MenuItemClasses;
use Municipio\Controller\Header\Helper\StickyResolver;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{
    public function testGetMenuItemsByAlignmentGroupsItemsByPlacementAlignment(): void
    {
        $header = $this->createHeader((object) [
            'headerSortableSectionMainUpper' => json_encode(['logotype', 'language', 'primary']),
            'headerSortableSectionMainUpperResponsive' => json_encode(['logotype', 'language', 'primary']),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                    'language' => ['align' => 'right', 'margin' => 'none'],
                    'primary' => ['align' => 'center', 'margin' => 'none'],
                ],
                'header_sortable_section_main_upper_responsive' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                    'language' => ['align' => 'right', 'margin' => 'none'],
                    'primary' => ['align' => 'center', 'margin' => 'none'],
                ],
            ]),
            'headerSticky' => '',
        ]);

        $groupedItems = $header->getMenuItemsByAlignment();

        static::assertArrayHasKey('left', $groupedItems);
        static::assertArrayHasKey('center', $groupedItems);
        static::assertArrayHasKey('right', $groupedItems);
        static::assertArrayHasKey('logotype', $groupedItems['left']);
        static::assertArrayHasKey('primary', $groupedItems['center']);
        static::assertArrayHasKey('language', $groupedItems['right']);
        static::assertTrue($header->hasMenuItemsForAlignment('center'));
    }

    public function testGetCssClassesAddsCenteredContentModifierWhenCenterAreaHasItems(): void
    {
        $header = $this->createHeader((object) [
            'headerSortableSectionMainUpper' => json_encode(['primary']),
            'headerSortableSectionMainUpperResponsive' => json_encode([]),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'primary' => ['align' => 'center', 'margin' => 'none'],
                ],
            ]),
            'headerSticky' => '',
        ]);

        $cssClasses = $header->getCssClasses();

        static::assertContains('c-header--flexible-has-centered-content', $cssClasses);
    }

    private function createHeader(object $customizer): Header
    {
        $extractMenuItems = new ExtractMenuItems($customizer);

        return new Header(
            'upper',
            new MenuItemFactory(new MenuItemClasses()),
            $extractMenuItems,
            new HeaderClasses(),
            new HeaderAttributes(),
            new StickyResolver($extractMenuItems, $customizer),
        );
    }
}
