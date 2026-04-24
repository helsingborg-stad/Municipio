<?php

declare(strict_types=1);

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__ as Translate;
use WpService\Contracts\GetOption;
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
            new class() implements GetPostType, GetPostTypeObject, GetPostTypeArchiveLink, IsArchive, GetQueriedObject, GetTheTitle, GetOption, Translate {
                public function getPostType(int|\WP_Post|null $post = null): string|false
                {
                    return 'akademi-utbildning';
                }

                public function getPostTypeObject(string $postType): ?\WP_Post_Type
                {
                    $t = new \WP_Post_Type('post');
                    $t->label = 'Akademi, utbildning & forskning';
                    return $t;
                }

                public function getPostTypeArchiveLink(string $postType): string|false
                {
                    return 'https://example.com/en/talent-research';
                }

                public function isArchive(): bool
                {
                    return true;
                }

                public function getQueriedObject(): \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null
                {
                    $t = new \WP_Post_Type('post');
                    $t->name = 'akademi-utbildning';
                    $t->label = 'Akademi, utbildning & forskning';
                    return $t;
                }

                public function getTheTitle(int|\WP_Post $post = 0): string
                {
                    return $post === 10748 ? 'Talent & Research' : '';
                }

                public function getOption(string $option, mixed $defaultValue = false): mixed
                {
                    return false;
                }

                public function __(string $text, string $domain = 'default'): string
                {
                    return $text;
                }
            },
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
            new class() implements GetPostType, GetPostTypeObject, GetPostTypeArchiveLink, IsArchive, GetQueriedObject, GetTheTitle, GetOption, Translate {
                public function getPostType(int|\WP_Post|null $post = null): string|false
                {
                    return 'akademi-utbildning';
                }

                public function getPostTypeObject(string $postType): ?\WP_Post_Type
                {
                    $t = new \WP_Post_Type('post');
                    $t->label = 'Akademi, utbildning & forskning';
                    return $t;
                }

                public function getPostTypeArchiveLink(string $postType): string|false
                {
                    return 'https://example.com/en/talent-research';
                }

                public function isArchive(): bool
                {
                    return true;
                }

                public function getQueriedObject(): \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null
                {
                    $t = new \WP_Post_Type('post');
                    $t->name = 'akademi-utbildning';
                    $t->label = 'Akademi, utbildning & forskning';
                    return $t;
                }

                public function getTheTitle(int|\WP_Post $post = 0): string
                {
                    return '';
                }

                public function getOption(string $option, mixed $defaultValue = false): mixed
                {
                    return false;
                }

                public function __(string $text, string $domain = 'default'): string
                {
                    return $text;
                }
            },
        );

        $menu = $sut->getMenu();

        $this->assertSame('Akademi, utbildning & forskning', $menu['items'][0]['label']);
    }

    #[TestDox('getMenu() uses the page_for_posttype title as label on single post pages (Polylang-friendly)')]
    public function testGetMenuUsesPageForPostTypeTitleOnSinglePost(): void
    {
        CurrentPostId::$pageId = 99; // current single post, NOT the archive page

        $sut = new AppendArchiveMenuItem(
            $this->createInnerMenu(),
            new class() implements GetPostType, GetPostTypeObject, GetPostTypeArchiveLink, IsArchive, GetQueriedObject, GetTheTitle, GetOption, Translate {
                public function getPostType(int|\WP_Post|null $post = null): string|false
                {
                    return 'food-drinks';
                }

                public function getPostTypeObject(string $postType): ?\WP_Post_Type
                {
                    $t = new \WP_Post_Type('post');
                    $t->label = 'Mat och dryck';
                    return $t; // Swedish raw label
                }

                public function getPostTypeArchiveLink(string $postType): string|false
                {
                    return 'https://example.com/en/visit-experience/food-drinks/';
                }

                public function isArchive(): bool
                {
                    return false;
                }

                public function getQueriedObject(): \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null
                {
                    $t = new \WP_Post_Type('post');
                    $t->name = 'food-drinks';
                    return $t;
                }

                public function getTheTitle(int|\WP_Post $post = 0): string
                {
                    // Polylang-translated title of the page_for_posttype page (ID=200)
                    return $post === 200 ? 'Food & Drinks' : '';
                }

                public function getOption(string $option, mixed $defaultValue = false): mixed
                {
                    return $option === 'page_for_food-drinks' ? 200 : false;
                }

                public function __(string $text, string $domain = 'default'): string
                {
                    return $text;
                }
            },
        );

        $menu = $sut->getMenu();

        $this->assertSame('Food & Drinks', $menu['items'][0]['label']);
    }

    #[TestDox('getMenu() falls back to post type object label when no page_for_posttype is set')]
    public function testGetMenuFallsBackToPostTypeLabelWhenNoArchivePage(): void
    {
        CurrentPostId::$pageId = 99;

        $sut = new AppendArchiveMenuItem(
            $this->createInnerMenu(),
            new class() implements GetPostType, GetPostTypeObject, GetPostTypeArchiveLink, IsArchive, GetQueriedObject, GetTheTitle, GetOption, Translate {
                public function getPostType(int|\WP_Post|null $post = null): string|false
                {
                    return 'food-drinks';
                }

                public function getPostTypeObject(string $postType): ?\WP_Post_Type
                {
                    $t = new \WP_Post_Type('post');
                    $t->label = 'Food & Drinks';
                    return $t;
                }

                public function getPostTypeArchiveLink(string $postType): string|false
                {
                    return 'https://example.com/en/food-drinks/';
                }

                public function isArchive(): bool
                {
                    return false;
                }

                public function getQueriedObject(): \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null
                {
                    $t = new \WP_Post_Type('post');
                    $t->name = 'food-drinks';
                    return $t;
                }

                public function getTheTitle(int|\WP_Post $post = 0): string
                {
                    return '';
                }

                public function getOption(string $option, mixed $defaultValue = false): mixed
                {
                    return false; // no page_for_posttype
                }

                public function __(string $text, string $domain = 'default'): string
                {
                    return $text;
                }
            },
        );

        $menu = $sut->getMenu();

        $this->assertSame('Food & Drinks', $menu['items'][0]['label']);
    }

    private function createInnerMenu(): MenuInterface
    {
        return new class() implements MenuInterface {
            public function getMenu(): array
            {
                return ['items' => []];
            }

            public function getConfig(): MenuConfigInterface
            {
                return new class() implements MenuConfigInterface {
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
