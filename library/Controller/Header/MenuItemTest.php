<?php

declare(strict_types=1);

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\Helper\Enums;
use Municipio\Controller\Header\Helper\MenuItemClasses;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{
    public function testMissingMobileBreakpointAddsMobileDisplayNoneClass(): void
    {
        $menuItem = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'right', 'order' => 0],
            ],
            new MenuItemClasses(),
        );

        $cssClasses = $menuItem->getCssClasses();

        static::assertContains('u-display--none', $cssClasses);
        static::assertContains('u-display--flex@lg', $cssClasses);
        static::assertContains('u-display--flex@xl', $cssClasses);
    }

    public function testMobileAlignmentFallsBackToDesktopWhenMobileIsMissing(): void
    {
        $menuItem = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'right', 'order' => 0],
                Enums::HEADER_BREAKPOINT::MOBILE->value => [],
            ],
            new MenuItemClasses(),
        );

        static::assertSame('right', $menuItem->getAlignment(Enums::HEADER_BREAKPOINT::MOBILE->value));
    }

    public function testCssClassesIdentifyItemType(): void
    {
        $menuItem = new MenuItem(
            'logotype',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'left', 'order' => 0],
                Enums::HEADER_BREAKPOINT::MOBILE->value => [],
            ],
            new MenuItemClasses(),
        );

        $cssClasses = $menuItem->getCssClasses();

        static::assertContains('c-header__item', $cssClasses);
        static::assertContains('c-header__item--logotype', $cssClasses);
    }

    public function testMobilePlacementClassesUseMobileAlignmentAndIndex(): void
    {
        $menuItem = new MenuItem(
            'drawer',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'left', 'order' => 0],
                Enums::HEADER_BREAKPOINT::MOBILE->value => ['align' => 'right', 'order' => 3],
            ],
            new MenuItemClasses(),
        );

        $cssClasses = $menuItem->getCssClasses();

        static::assertContains('c-header__item--mobile-right', $cssClasses);
        static::assertContains('c-header__item--mobile-index-3', $cssClasses);
        static::assertNotContains('c-header__item--mobile-left', $cssClasses);
    }

    public function testMobilePlacementClassesFallBackToDesktopAlignment(): void
    {
        $menuItem = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'center', 'order' => 0],
                Enums::HEADER_BREAKPOINT::MOBILE->value => ['order' => 1],
            ],
            new MenuItemClasses(),
        );

        static::assertContains('c-header__item--mobile-center', $menuItem->getCssClasses());
    }

    public function testNoMobilePlacementClassesWhenHiddenOnMobile(): void
    {
        $menuItem = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'center', 'order' => 0],
                Enums::HEADER_BREAKPOINT::MOBILE->value => [],
            ],
            new MenuItemClasses(),
        );

        $mobilePlacementClasses = array_filter(
            $menuItem->getCssClasses(),
            static fn (string $class): bool => str_starts_with($class, 'c-header__item--mobile-'),
        );

        static::assertSame([], $mobilePlacementClasses);
    }

    public function testInvalidMobileAlignmentAndIndexAreNormalized(): void
    {
        $menuItem = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => [],
                Enums::HEADER_BREAKPOINT::MOBILE->value => ['align' => 'bogus', 'order' => 999],
            ],
            new MenuItemClasses(),
        );

        $cssClasses = $menuItem->getCssClasses();

        static::assertContains('c-header__item--mobile-left', $cssClasses);
        static::assertContains('c-header__item--mobile-index-' . MenuItemClasses::MAX_MOBILE_INDEX, $cssClasses);
    }

    public function testCssClassesDoNotContainLegacyOrderOrAlignmentClasses(): void
    {
        $menuItem = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'right', 'order' => 0],
                Enums::HEADER_BREAKPOINT::MOBILE->value => ['align' => 'right', 'order' => 0],
            ],
            new MenuItemClasses(),
        );

        foreach ($menuItem->getCssClasses() as $class) {
            static::assertStringStartsNotWith('u-order--', $class);
            static::assertStringStartsNotWith('header-align--', $class);
        }
    }

    public function testMobileAlignmentUsesExplicitMobileValueWhenPresent(): void
    {
        $menuItem = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => ['align' => 'left', 'order' => 0],
                Enums::HEADER_BREAKPOINT::MOBILE->value => ['align' => 'center', 'order' => 0],
            ],
            new MenuItemClasses(),
        );

        static::assertSame('center', $menuItem->getAlignment(Enums::HEADER_BREAKPOINT::MOBILE->value));
    }

    public function testPlacementAlignmentUsesDesktopAndFallsBackToMobile(): void
    {
        $menuItemWithoutDesktop = new MenuItem(
            'primary',
            [
                Enums::HEADER_BREAKPOINT::DESKTOP->value => [],
                Enums::HEADER_BREAKPOINT::MOBILE->value => ['align' => 'center', 'order' => 0],
            ],
            new MenuItemClasses(),
        );

        static::assertSame('center', $menuItemWithoutDesktop->getPlacementAlignment());
    }
}
