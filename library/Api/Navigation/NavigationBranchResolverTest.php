<?php

declare(strict_types=1);

namespace Municipio\Api\Navigation;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NavigationBranchResolverTest extends TestCase
{
    #[TestDox('resolveMenuName() maps async identifiers to the same menu locations as the header controller')]
    public function testResolveMenuNameMapsKnownIdentifiers(): void
    {
        static::assertSame('secondary-menu', NavigationBranchResolver::resolveMenuName('mobile'));
        static::assertSame('main-menu', NavigationBranchResolver::resolveMenuName('primary'));
        static::assertSame('main-menu', NavigationBranchResolver::resolveMenuName('extended-dropdown-children'));
        static::assertSame('mega-menu', NavigationBranchResolver::resolveMenuName('mega-menu'));
        static::assertSame('', NavigationBranchResolver::resolveMenuName('sidebar'));
    }

    #[TestDox('findChildren() returns the matching nested branch children for a requested page ID')]
    public function testFindChildrenReturnsMatchingNestedBranchChildren(): void
    {
        $menuItems = [
            [
                'id' => 10739,
                'children' => [
                    ['id' => 10740, 'children' => false],
                    ['id' => 10741, 'children' => false],
                ],
            ],
            [
                'id' => 10748,
                'children' => [
                    [
                        'id' => 10749,
                        'children' => [
                            ['id' => 10750, 'children' => false],
                        ],
                    ],
                ],
            ],
        ];

        static::assertSame([10740, 10741], array_column((array) NavigationBranchResolver::findChildren($menuItems, 10739), 'id'));
        static::assertSame([10750], array_column((array) NavigationBranchResolver::findChildren($menuItems, 10749), 'id'));
    }

    #[TestDox('findChildren() returns an empty array when the requested node has no nested children')]
    public function testFindChildrenReturnsEmptyArrayWhenNodeHasNoChildren(): void
    {
        $menuItems = [
            ['id' => 10739, 'children' => false],
        ];

        static::assertSame([], NavigationBranchResolver::findChildren($menuItems, 10739));
    }

    #[TestDox('findChildren() returns null when the requested page ID does not exist in the menu tree')]
    public function testFindChildrenReturnsNullWhenNodeDoesNotExist(): void
    {
        static::assertNull(NavigationBranchResolver::findChildren([['id' => 10739, 'children' => false]], 99999));
    }
}