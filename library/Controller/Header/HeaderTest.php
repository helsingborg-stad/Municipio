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

    public function testItemsAreRenderedOnceAndPlacedByMobileSettingsOnMobile(): void
    {
        $header = $this->createHeader((object) [
            'headerSortableSectionMainUpper' => json_encode(['logotype', 'primary', 'drawer']),
            'headerSortableSectionMainUpperResponsive' => json_encode(['drawer', 'logotype']),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                    'primary' => ['align' => 'center', 'margin' => 'none'],
                    'drawer' => ['align' => 'right', 'margin' => 'none'],
                ],
                'header_sortable_section_main_upper_responsive' => [
                    'drawer' => ['align' => 'left', 'margin' => 'none'],
                    'logotype' => ['align' => 'center', 'margin' => 'none'],
                ],
            ]),
            'headerSticky' => '',
        ]);

        $groupedItems = $header->getMenuItemsByAlignment();

        // Each item appears exactly once, in its desktop alignment wrapper.
        static::assertSame(['logotype'], array_keys($groupedItems['left']));
        static::assertSame(['primary'], array_keys($groupedItems['center']));
        static::assertSame(['drawer'], array_keys($groupedItems['right']));

        $drawerClasses = $groupedItems['right']['drawer']->getCssClasses();

        $logotypeClasses = $groupedItems['left']['logotype']->getCssClasses();
        static::assertContains('c-header__item--mobile-center', $logotypeClasses);
        static::assertContains('c-header__item--mobile-index-1', $logotypeClasses);

        $primaryClasses = $groupedItems['center']['primary']->getCssClasses();
        static::assertContains('u-display--none', $primaryClasses);
        static::assertNotContains('c-header__item--mobile-center', $primaryClasses);
    }

    public function testMobileItemsFallbackToDesktopMetadataWhenResponsiveMetadataIsMissing(): void
    {
        $header = $this->createHeader((object) [
            'headerSortableSectionMainUpper' => json_encode(['logotype', 'search-modal', 'language', 'drawer']),
            'headerSortableSectionMainUpperResponsive' => json_encode(['logotype', 'drawer']),
            'headerSortableHiddenStorage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                    'search-modal' => ['align' => 'left', 'margin' => 'none'],
                    'language' => ['align' => 'right', 'margin' => 'none'],
                    'drawer' => ['align' => 'right', 'margin' => 'none'],
                ],
                'header_sortable_section_main_upper_responsive' => [],
            ]),
            'headerSticky' => '',
        ]);

        $groupedItems = $header->getMenuItemsByAlignment();

        static::assertArrayHasKey('logotype', $groupedItems['left']);
        static::assertArrayHasKey('drawer', $groupedItems['right']);

        $logotypeClasses = $groupedItems['left']['logotype']->getCssClasses();
        $drawerClasses = $groupedItems['right']['drawer']->getCssClasses();

        static::assertContains('u-display--flex', $logotypeClasses);
        static::assertContains('u-display--flex', $drawerClasses);
        static::assertContains('c-header__item--mobile-right', $drawerClasses);
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
