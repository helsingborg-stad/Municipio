<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolvePageTreeMenuPageIdsTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolvePageTreeMenuPageIds::class, $this->getSut());
    }

    #[TestDox('addHooks() registers filters for page_on_front, page_for_posts, and navigation cache key')]
    public function testAddHooksRegistersFiltersForPageOnFrontAndPageForPosts(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        $sut = new ResolvePageTreeMenuPageIds($wpService);

        $sut->addHooks();

        static::assertSame(
            ['option_page_on_front', 'option_page_for_posts', 'Municipio/Navigation/Cache/Key'],
            array_column($wpService->methodCalls['addFilter'], 0)
        );
    }

    #[TestDox('resolveTranslatedPageId() returns the translated page ID when Polylang resolves one')]
    public function testResolveTranslatedPageIdReturnsTranslatedPageId(): void
    {
        $sut = $this->getSut(
            translatedPostResolver: static fn (int $pageId): int => $pageId + 100
        );

        static::assertSame(110, $sut->resolveTranslatedPageId(10));
    }

    #[TestDox('resolveTranslatedPageId() returns the original value when the page ID is not numeric')]
    public function testResolveTranslatedPageIdReturnsOriginalValueWhenPageIdIsNotNumeric(): void
    {
        $sut = $this->getSut();

        static::assertSame('not-a-page-id', $sut->resolveTranslatedPageId('not-a-page-id'));
    }

    #[TestDox('resolveTranslatedPageId() returns the original page ID when no translated page is found')]
    public function testResolveTranslatedPageIdReturnsOriginalPageIdWhenNoTranslatedPageIsFound(): void
    {
        $sut = $this->getSut(
            translatedPostResolver: static fn (int $pageId): int => 0
        );

        static::assertSame(10, $sut->resolveTranslatedPageId(10));
    }

    #[TestDox('addLanguageToCacheKey() appends the current language to the cache key')]
    public function testAddLanguageToCacheKeyAppendsLanguage(): void
    {
        $sut = $this->getSut(currentLanguageResolver: static fn (): string => 'en');

        static::assertSame('pageForPostType:v2:en', $sut->addLanguageToCacheKey('pageForPostType:v2'));
    }

    #[TestDox('addLanguageToCacheKey() returns the original key when no language is resolved')]
    public function testAddLanguageToCacheKeyReturnsOriginalKeyWhenNoLanguage(): void
    {
        $sut = $this->getSut(currentLanguageResolver: static fn (): string => '');

        static::assertSame('pageForPostType:v2', $sut->addLanguageToCacheKey('pageForPostType:v2'));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     *
     * @return ResolvePageTreeMenuPageIds The system under test.
     */
    private function getSut(
        ?Closure $translatedPostResolver = null,
        ?Closure $currentLanguageResolver = null
    ): ResolvePageTreeMenuPageIds {
        return new ResolvePageTreeMenuPageIds(
            new FakeWpService(['addFilter' => true]),
            $translatedPostResolver,
            $currentLanguageResolver
        );
    }
}
