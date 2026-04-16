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

    #[TestDox('addHooks() registers filters for translated page tree related options')]
    public function testAddHooksRegistersFiltersForTranslatedPageTreeRelatedOptions(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getPostTypes' => ['page', 'event', 'news'],
        ]);

        $sut = new ResolvePageTreeMenuPageIds($wpService);

        $sut->addHooks();

        static::assertSame(
            [
                'option_page_on_front',
                'option_page_for_posts',
                'option_page_for_page',
                'option_page_for_event',
                'option_page_for_news',
            ],
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

    /**
     * Get the system under test.
     *
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     *
     * @return ResolvePageTreeMenuPageIds The system under test.
     */
    private function getSut(?Closure $translatedPostResolver = null): ResolvePageTreeMenuPageIds
    {
        return new ResolvePageTreeMenuPageIds(
            new FakeWpService([
                'addFilter' => true,
                'getPostTypes' => ['page', 'news'],
            ]),
            $translatedPostResolver
        );
    }
}
