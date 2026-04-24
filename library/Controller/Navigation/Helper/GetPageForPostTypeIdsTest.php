<?php

declare(strict_types=1);

namespace Municipio\Controller\Navigation\Helper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPageForPostTypeIdsTest extends TestCase
{
    #[TestDox('getPageForPostTypeIds() includes mapped public post types even when they are not hierarchical')]
    public function testGetPageForPostTypeIdsIncludesNonHierarchicalPostTypes(): void
    {
        $pageForPostTypeIds = GetPageForPostTypeIds::getPageForPostTypeIds(
            ['page', 'investera-etablera', 'post'],
            static fn (string $postType): mixed => match ($postType) {
                'investera-etablera' => 10739,
                'post' => 0,
                default => 17,
            },
            true
        );

        static::assertSame(
            [
                17 => 'page',
                10739 => 'investera-etablera',
            ],
            $pageForPostTypeIds
        );
    }
}