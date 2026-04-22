<?php

declare(strict_types=1);

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPostType;
use WpService\Contracts\GetPostTypeArchiveLink;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetQueriedObject;
use WpService\Contracts\GetTheTitle;
use WpService\Contracts\IsArchive;

class AppendArchiveMenuItemTest extends TestCase
{
    protected function tearDown(): void
    {
        CurrentPostId::$pageId = 0;
    }

    #[TestDox('getMenu() prefers the current archive page title over the queried object label')]
    public function testGetMenuPrefersCurrentArchivePageTitle(): void
    {
        CurrentPostId::$pageId = 10748;

        $sut = new AppendArchiveMenuItem(
            $this->createInnerMenu(),
            new class () implements GetPostType, GetPostTypeObject, GetPostTypeArchiveLink, IsArchive, GetQueriedObject, GetTheTitle {
                public function getPostType(int|\WP_Post|null $post = null): string|false
                {
                    return 'akademi-utbildning';
                }

                public function getPostTypeObject(string $postType): object|false
                {
                    return (object) ['label' => 'Akademi, utbildning & forskning'];
                }

                public function getPostTypeArchiveLink(string $postType): string|false
                {
                    return 'https://example.com/en/talent-research';
                }

                public function isArchive(): bool
                {
                    return true;
                }

                public function getQueriedObject(): object
                {
                    return (object) [
                        'name' => 'akademi-utbildning',
                        'label' => 'Akademi, utbildning & forskning',
                    ];
                }

                public function getTheTitle(int $postId = 0): string
                {
                    return $postId === 10748 ? 'Talent & Research' : '';
                }
            }
        );

        $menu = $sut->getMenu();

        $this->assertSame('Talent & Research', $menu['items'][0]['label']);
        $this->assertSame('https://example.com/en/talent-research', $menu['items'][0]['href']);
    }

    #[TestDox('getMenu() falls back to the queried object label when no current archive page title exists')]
    public function testGetMenuFallsBackToQueriedObjectLabel(): void
    {
        CurrentPostId::$pageId = 10748;

        $sut = new AppendArchiveMenuItem(
            $this->createInnerMenu(),
            new class () implements GetPostType, GetPostTypeObject, GetPostTypeArchiveLink, IsArchive, GetQueriedObject, GetTheTitle {
                public function getPostType(int|\WP_Post|null $post = null): string|false
                {
                    return 'akademi-utbildning';
                }

                public function getPostTypeObject(string $postType): object|false
                {
                    return (object) ['label' => 'Akademi, utbildning & forskning'];
                }

                public function getPostTypeArchiveLink(string $postType): string|false
                {
                    return 'https://example.com/en/talent-research';
                }

                public function isArchive(): bool
                {
                    return true;
                }

                public function getQueriedObject(): object
                {
                    return (object) [
                        'name' => 'akademi-utbildning',
                        'label' => 'Akademi, utbildning & forskning',
                    ];
                }

                public function getTheTitle(int $postId = 0): string
                {
                    return '';
                }
            }
        );

        $menu = $sut->getMenu();

        $this->assertSame('Akademi, utbildning & forskning', $menu['items'][0]['label']);
    }

    private function createInnerMenu(): MenuInterface
    {
        return new class () implements MenuInterface {
            public function getMenu(): array
            {
                return ['items' => []];
            }

            public function getConfig(): MenuConfigInterface
            {
                return new class () implements MenuConfigInterface {
                    public function getIdentifier(): string
                    {
                        return 'breadcrumb';
                    }

                    public function getMenuName(): string|int
                    {
                        return '';
                    }

                    public function getRemoveSubLevels(): bool
                    {
                        return false;
                    }

                    public function getRemoveTopLevel(): bool
                    {
                        return false;
                    }

                    public function getFallbackToPageTree(): bool|int
                    {
                        return false;
                    }
                };
            }
        };
    }
}