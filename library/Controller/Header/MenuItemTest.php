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

        $cssClasses = $menuItem->getCssClasses();
        static::assertContains('u-display--none', $cssClasses);
        static::assertContains('header-align--right', $cssClasses);
        static::assertContains('u-order--200', $cssClasses);
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
