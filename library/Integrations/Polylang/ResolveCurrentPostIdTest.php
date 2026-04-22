<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveCurrentPostIdTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveCurrentPostId::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the current post ID filter')]
    public function testAddHooksRegistersCurrentPostIdFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolveCurrentPostId($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Helper/CurrentPostId', [$sut, 'resolveCurrentPostId']]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('resolveCurrentPostId() returns the translated archive page ID when Polylang resolves one')]
    public function testResolveCurrentPostIdReturnsTranslatedArchivePageId(): void
    {
        $sut = $this->getSut(
            isPostTypeArchiveResolver: static fn (): bool => true,
            currentPostTypeResolver: static fn (): string => 'akademi-utbildning',
            archivePageIdResolver: static fn (string $postType): int => 10448,
            translatedPostResolver: static fn (int $postId): int => 10748
        );

        static::assertSame(10748, $sut->resolveCurrentPostId(2));
    }

    #[TestDox('resolveCurrentPostId() falls back to the localized archive URL page ID when no archive option exists')]
    public function testResolveCurrentPostIdFallsBackToLocalizedArchiveUrlPageId(): void
    {
        $sut = $this->getSut(
            isPostTypeArchiveResolver: static fn (): bool => true,
            currentPostTypeResolver: static fn (): string => 'akademi-utbildning',
            archivePageIdResolver: static fn (string $postType): int => 0,
            translatedPostResolver: static fn (int $postId): int => 0,
            currentArchivePageIdResolver: static fn (): int => 10748
        );

        static::assertSame(10748, $sut->resolveCurrentPostId(2));
    }

    #[TestDox('resolveCurrentPostId() returns the original ID when the request is not a post type archive')]
    public function testResolveCurrentPostIdReturnsOriginalIdWhenNotArchive(): void
    {
        $sut = $this->getSut(
            isPostTypeArchiveResolver: static fn (): bool => false
        );

        static::assertSame(2, $sut->resolveCurrentPostId(2));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $isPostTypeArchiveResolver Optional archive resolver.
     * @param ?Closure $currentPostTypeResolver Optional current post type resolver.
     * @param ?Closure $archivePageIdResolver Optional page-for-post-type resolver.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $currentArchivePageIdResolver Optional localized archive page ID resolver.
     *
     * @return ResolveCurrentPostId
     */
    private function getSut(
        ?Closure $isPostTypeArchiveResolver = null,
        ?Closure $currentPostTypeResolver = null,
        ?Closure $archivePageIdResolver = null,
        ?Closure $translatedPostResolver = null,
        ?Closure $currentArchivePageIdResolver = null
    ): ResolveCurrentPostId {
        return new ResolveCurrentPostId(
            new FakeWpService([
                'addFilter' => true,
            ]),
            $isPostTypeArchiveResolver,
            $currentPostTypeResolver,
            $archivePageIdResolver,
            $translatedPostResolver,
            $currentArchivePageIdResolver
        );
    }
}