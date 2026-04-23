<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolvePageForPostTypeIdsTest extends TestCase
{
    #[TestDox('addHooks() registers the page-for-post-type ID filter')]
    public function testAddHooksRegistersPageForPostTypeIdFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        $sut = new ResolvePageForPostTypeIds($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Navigation/PageForPostTypeId', [$sut, 'resolveTranslatedPageId'], 10, 2]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('resolveTranslatedPageId() returns the translated page-for-post-type ID when available')]
    public function testResolveTranslatedPageIdReturnsTranslatedPageId(): void
    {
        $sut = $this->getSut(
            static fn (int $pageId): int => $pageId === 27 ? 10739 : 0
        );

        static::assertSame(10739, $sut->resolveTranslatedPageId(27, 'investera-etablera'));
    }

    #[TestDox('resolveTranslatedPageId() returns the original page ID when no translation exists')]
    public function testResolveTranslatedPageIdReturnsOriginalPageIdWhenNoTranslationExists(): void
    {
        $sut = $this->getSut(static fn (int $pageId): int => 0);

        static::assertSame(27, $sut->resolveTranslatedPageId(27, 'investera-etablera'));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     *
     * @return ResolvePageForPostTypeIds
     */
    private function getSut(?Closure $translatedPostResolver = null): ResolvePageForPostTypeIds
    {
        return new ResolvePageForPostTypeIds(
            new FakeWpService(['addFilter' => true]),
            $translatedPostResolver
        );
    }
}