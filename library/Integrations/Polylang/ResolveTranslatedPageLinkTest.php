<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Implementations\FakeWpService;

class ResolveTranslatedPageLinkTest extends TestCase
{
    #[TestDox('addHooks() registers the page_link filter')]
    public function testAddHooksRegistersPageLinkFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getPost' => new WP_Post((object) ['ID' => 1]),
            'homeUrl' => 'https://example.com',
        ]);

        $sut = new ResolveTranslatedPageLink($wpService);

        $sut->addHooks();

        static::assertSame(
            [['page_link', [$sut, 'resolveTranslatedPageLink'], 10, 3]],
            $wpService->methodCalls['addFilter'],
        );
    }

    #[TestDox('resolveTranslatedPageLink() rebuilds hierarchical page links from translated ancestor slugs')]
    public function testResolveTranslatedPageLinkRebuildsHierarchicalPath(): void
    {
        $sut = $this->getSut(
            postTranslationsResolver: static function (int $postId): array {
                return match ($postId) {
                    200 => ['sv' => 100, 'en' => 200],
                    100 => ['sv' => 100, 'en' => 200],
                    50 => ['sv' => 50, 'en' => 150],
                    default => [],
                };
            },
            translatedPostResolver: static function (int $postId, string $language): int {
                return match ([$postId, $language]) {
                    [100, 'en'] => 200,
                    [50, 'en'] => 150,
                    default => 0,
                };
            },
            postLanguageResolver: static fn(int $postId): string => 'en',
            defaultLanguageResolver: static fn(): string => 'sv',
            languageHomeUrlResolver: static fn(string $language): string => 'http://localhost:8080/hbgcom/en',
        );

        $resolvedLink = $sut->resolveTranslatedPageLink(
            'http://localhost:8080/hbgcom/en/leva-bo/move-here/',
            200,
            false,
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/live-stay-studdy/move-here/',
            $resolvedLink,
        );
    }

    #[TestDox('resolveTranslatedPageLink() returns the original link when it is already the language home URL (front page)')]
    public function testResolveTranslatedPageLinkReturnsFrontPageLinkUnchanged(): void
    {
        $sut = $this->getSut(
            postLanguageResolver: static fn(int $postId): string => 'en',
            languageHomeUrlResolver: static fn(string $language): string => 'http://localhost:8080/hbgcom/en',
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/', 1, false),
        );
    }

    #[TestDox('resolveTranslatedPageLink() uses only the translated slug when the translation is shallower than the source hierarchy')]
    public function testResolveTranslatedPageLinkHandlesShallowerTranslatedHierarchy(): void
    {
        // Source (sv): wechosehelsingborg (300) under leva-bo (50) — leva-bo has no English translation.
        // Translated (en): live-stay-studdy (400) at top level (post_parent=0).
        $sut = $this->getSut(
            postTranslationsResolver: static function (int $postId): array {
                return match ($postId) {
                    300 => ['sv' => 300, 'en' => 400],
                    400 => ['sv' => 300, 'en' => 400],
                    default => [],
                };
            },
            translatedPostResolver: static function (int $postId, string $language): int {
                return match ([$postId, $language]) {
                    [300, 'en'] => 400,
                    default => 0,
                };
            },
            postLanguageResolver: static fn(int $postId): string => 'en',
            defaultLanguageResolver: static fn(): string => 'sv',
            languageHomeUrlResolver: static fn(string $language): string => 'http://localhost:8080/hbgcom/en',
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/live-stay-studdy/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/leva-bo/wechosehelsingborg/', 400, false),
        );
    }

    #[TestDox('resolveTranslatedPageLink() skips the front page ancestor when building the path')]
    public function testResolveTranslatedPageLinkSkipsFrontPageAncestor(): void
    {
        // English archive page (500) has post_parent pointing to the English front page (999).
        // The front page should not appear as a path segment.
        $sut = $this->getSut(
            postTranslationsResolver: static function (int $postId): array {
                return match ($postId) {
                    500 => ['sv' => 500, 'en' => 500],
                    999 => ['sv' => 999, 'en' => 999],
                    default => [],
                };
            },
            translatedPostResolver: static function (int $postId, string $language): int {
                return 0;
            },
            postLanguageResolver: static fn(int $postId): string => 'en',
            defaultLanguageResolver: static fn(): string => 'sv',
            languageHomeUrlResolver: static fn(string $language): string => 'http://localhost:8080/hbgcom/en',
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/live-stay-studdy/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/home/live-stay-studdy/', 500, false),
        );
    }

    #[TestDox('resolveTranslatedPageLink() skips the front page ancestor found via translatedPostResolver when current language differs from target language')]
    public function testResolveTranslatedPageLinkSkipsFrontPageFoundViaTranslatedPostResolver(): void
    {
        // Simulates building an English link while Polylang's current language is Swedish:
        // - page_on_front = 1 (Swedish startsida, because pll_current_language()='sv')
        // - translatedPostResolver(1, 'en') = 2 (English home, linked in Polylang)
        // - English food-drinks (200) has no Swedish counterpart → sourcePostId = 200
        // - English hierarchy: home(2) → visit-experience(150) → food-drinks(200)
        // The English "home" page must be recognised as a front page and excluded.
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getOption' => static fn(string $option): mixed => match ($option) {
                'page_on_front' => 1, // Swedish front page (current language is 'sv')
                default => false,
            },
            'getPost' => static function (int $postId): ?WP_Post {
                $posts = [
                    200 => ['ID' => 200, 'post_parent' => 150, 'post_name' => 'food-drinks'],
                    150 => ['ID' => 150, 'post_parent' => 2, 'post_name' => 'visit-experience'],
                    2 => ['ID' => 2, 'post_parent' => 0, 'post_name' => 'home'],
                    1 => ['ID' => 1, 'post_parent' => 0, 'post_name' => 'startsida'],
                ];

                if (!isset($posts[$postId])) {
                    return null;
                }

                $post = new WP_Post((object) $posts[$postId]);
                foreach ($posts[$postId] as $key => $value) {
                    $post->$key = $value;
                }

                return $post;
            },
            'homeUrl' => 'http://localhost:8080/hbgcom',
        ]);

        $sut = new ResolveTranslatedPageLink(
            $wpService,
            // postTranslationsResolver: food-drinks (200) has no Swedish counterpart.
            // The Swedish front page (1) has no English translation via this resolver.
            static fn(int $postId): array => match ($postId) {
                1 => ['sv' => 1],
                default => [],
            },
            // translatedPostResolver: pll_get_post(1, 'en') = 2 (English home).
            static fn(int $postId, string $language): int => match ([$postId, $language]) {
                [1, 'en'] => 2,
                default => 0,
            },
            static fn(int $postId): string => 'en',
            static fn(): string => 'sv',
            static fn(string $lang): string => 'http://localhost:8080/hbgcom/en',
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/visit-experience/food-drinks/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/home/visit-experience/food-drinks/', 200, false),
        );
    }

    #[TestDox('resolveTranslatedPageLink() returns the original link when the page language cannot be resolved')]
    public function testResolveTranslatedPageLinkReturnsOriginalLinkWhenLanguageIsUnknown(): void
    {
        $sut = $this->getSut(postLanguageResolver: static fn(int $postId): string => '');

        static::assertSame(
            'http://localhost:8080/hbgcom/en/leva-bo/move-here/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/leva-bo/move-here/', 200, false),
        );
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $postTranslationsResolver Optional post translations resolver.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $postLanguageResolver Optional post language resolver.
     * @param ?Closure $defaultLanguageResolver Optional default language resolver.
     * @param ?Closure $languageHomeUrlResolver Optional language home URL resolver.
     *
     * @return ResolveTranslatedPageLink
     */
    private function getSut(
        ?Closure $postTranslationsResolver = null,
        ?Closure $translatedPostResolver = null,
        ?Closure $postLanguageResolver = null,
        ?Closure $defaultLanguageResolver = null,
        ?Closure $languageHomeUrlResolver = null,
    ): ResolveTranslatedPageLink {
        return new ResolveTranslatedPageLink(
            new FakeWpService([
                'addFilter' => true,
                'getOption' => static fn(string $option): mixed => match ($option) {
                    'page_on_front' => 999,
                    default => false,
                },
                'getPost' => static function (int $postId): ?WP_Post {
                    $posts = [
                        200 => ['ID' => 200, 'post_parent' => 50, 'post_name' => 'move-here'],
                        150 => ['ID' => 150, 'post_parent' => 0, 'post_name' => 'live-stay-studdy'],
                        100 => ['ID' => 100, 'post_parent' => 50, 'post_name' => 'flytta-hit'],
                        50 => ['ID' => 50, 'post_parent' => 0, 'post_name' => 'leva-bo'],
                        400 => ['ID' => 400, 'post_parent' => 0, 'post_name' => 'live-stay-studdy'],
                        500 => ['ID' => 500, 'post_parent' => 999, 'post_name' => 'live-stay-studdy'],
                        999 => ['ID' => 999, 'post_parent' => 0, 'post_name' => 'home'],
                    ];

                    if (!isset($posts[$postId])) {
                        return null;
                    }

                    $post = new WP_Post((object) $posts[$postId]);
                    foreach ($posts[$postId] as $key => $value) {
                        $post->$key = $value;
                    }

                    return $post;
                },
                'homeUrl' => 'http://localhost:8080/hbgcom',
            ]),
            $postTranslationsResolver,
            $translatedPostResolver,
            $postLanguageResolver,
            $defaultLanguageResolver,
            $languageHomeUrlResolver,
        );
    }
}
