<?php

declare(strict_types=1);

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PageTreeAppendChildrenTest extends TestCase
{
    protected function tearDown(): void
    {
        CurrentPostId::$pageId = 0;
    }

    #[TestDox('getMenu() normalizes translated fallback children so they can be structured later')]
    public function testGetMenuNormalizesTranslatedFallbackChildren(): void
    {
        CurrentPostId::$pageId = 128;

        $inner = new class implements MenuInterface {
            public function getMenu(): array
            {
                return [
                    'items' => [
                        [
                            'id' => 128,
                            'post_parent' => 0,
                            'post_type' => 'page',
                            'active' => true,
                            'ancestor' => false,
                            'label' => 'Why Helsingborg',
                            'children' => false,
                        ],
                    ],
                ];
            }

            public function getConfig(): MenuConfigInterface
            {
                return new MenuConfig('mobile', '', false, false, true);
            }
        };

        $wpService = new FakeWpService([
            'getPostType' => 'page',
            'applyFilters' => static fn (string $hookName, array $children, int $postId): array => $hookName === 'Municipio/Navigation/PageTree/Children' && $postId === 128
                ? [
                    [
                        'ID' => 124,
                        'post_title' => 'Invest in Helsingborg',
                        'post_parent' => 128,
                        'post_type' => 'page',
                        'active' => false,
                        'ancestor' => false,
                    ],
                ]
                : $children,
        ]);

        $menu = (new PageTreeAppendChildren($inner, $wpService))->getMenu();

        static::assertTrue($menu['items'][0]['children']);
        static::assertSame(124, $menu['items'][1]['id']);
        static::assertSame('Invest in Helsingborg', $menu['items'][1]['label']);
        static::assertSame(128, $menu['items'][1]['post_parent']);
        static::assertArrayNotHasKey('ID', $menu['items'][1]);
    }
}