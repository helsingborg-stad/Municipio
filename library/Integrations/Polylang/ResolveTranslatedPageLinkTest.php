<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use stdClass;
use WpService\Implementations\FakeWpService;

class ResolveTranslatedPageLinkTest extends TestCase
{
    #[TestDox('addHooks() registers the page_link filter')]
    public function testAddHooksRegistersPageLinkFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getPost' => new stdClass(),
            'homeUrl' => 'https://example.com',
        ]);

        $sut = new ResolveTranslatedPageLink($wpService);

        $sut->addHooks();

        static::assertSame(
            [['page_link', [$sut, 'resolveTranslatedPageLink'], 10, 3]],
            $wpService->methodCalls['addFilter']
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
            postLanguageResolver: static fn (int $postId): string => 'en',
            defaultLanguageResolver: static fn (): string => 'sv',
            languageHomeUrlResolver: static fn (string $language): string => 'http://localhost:8080/hbgcom/en'
        );

        $resolvedLink = $sut->resolveTranslatedPageLink(
            'http://localhost:8080/hbgcom/en/leva-bo/move-here/',
            200,
            false
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/live-stay-studdy/move-here/',
            $resolvedLink
        );
    }

    #[TestDox('resolveTranslatedPageLink() returns the original link when it is already the language home URL (front page)')]
    public function testResolveTranslatedPageLinkReturnsFrontPageLinkUnchanged(): void
    {
        $sut = $this->getSut(
            postLanguageResolver:    static fn (int $postId): string => 'en',
            languageHomeUrlResolver: static fn (string $language): string => 'http://localhost:8080/hbgcom/en'
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/', 1, false)
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
                    default     => 0,
                };
            },
            postLanguageResolver:    static fn (int $postId): string => 'en',
            defaultLanguageResolver: static fn (): string => 'sv',
            languageHomeUrlResolver: static fn (string $language): string => 'http://localhost:8080/hbgcom/en'
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/en/live-stay-studdy/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/leva-bo/wechosehelsingborg/', 400, false)
        );
    }

    #[TestDox('resolveTranslatedPageLink() returns the original link when the page language cannot be resolved')]
    public function testResolveTranslatedPageLinkReturnsOriginalLinkWhenLanguageIsUnknown(): void
    {
        $sut = $this->getSut(postLanguageResolver: static fn (int $postId): string => '');

        static::assertSame(
            'http://localhost:8080/hbgcom/en/leva-bo/move-here/',
            $sut->resolveTranslatedPageLink('http://localhost:8080/hbgcom/en/leva-bo/move-here/', 200, false)
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
        ?Closure $languageHomeUrlResolver = null
    ): ResolveTranslatedPageLink {
        return new ResolveTranslatedPageLink(
            new FakeWpService([
                'addFilter' => true,
                'getPost' => static function (int $postId): ?object {
                    return match ($postId) {
                        200 => (object) ['ID' => 200, 'post_parent' => 50,  'post_name' => 'move-here'],
                        150 => (object) ['ID' => 150, 'post_parent' => 0,   'post_name' => 'live-stay-studdy'],
                        100 => (object) ['ID' => 100, 'post_parent' => 50,  'post_name' => 'flytta-hit'],
                        50  => (object) ['ID' => 50,  'post_parent' => 0,   'post_name' => 'leva-bo'],
                        400 => (object) ['ID' => 400, 'post_parent' => 0,   'post_name' => 'live-stay-studdy'],
                        default => null,
                    };
                },
                'homeUrl' => 'http://localhost:8080/hbgcom',
            ]),
            $postTranslationsResolver,
            $translatedPostResolver,
            $postLanguageResolver,
            $defaultLanguageResolver,
            $languageHomeUrlResolver
        );
    }
}