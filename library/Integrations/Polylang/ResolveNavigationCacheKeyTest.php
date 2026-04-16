<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveNavigationCacheKeyTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveNavigationCacheKey::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the navigation cache key filter')]
    public function testAddHooksRegistersNavigationCacheKeyFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolveNavigationCacheKey($wpService);

        $sut->addHooks();

        static::assertSame(
            ['Municipio/Navigation/Cache/Key'],
            array_column($wpService->methodCalls['addFilter'], 0)
        );
    }

    #[TestDox('appendCurrentLanguage() appends the current language to the cache key')]
    public function testAppendCurrentLanguageAppendsCurrentLanguageToCacheKey(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv'
        );

        static::assertSame('pageForPostType:sv', $sut->appendCurrentLanguage('pageForPostType'));
    }

    #[TestDox('appendCurrentLanguage() returns the original cache key when no language is available')]
    public function testAppendCurrentLanguageReturnsOriginalCacheKeyWhenNoLanguageIsAvailable(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => ''
        );

        static::assertSame('pageForPostType', $sut->appendCurrentLanguage('pageForPostType'));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     *
     * @return ResolveNavigationCacheKey The system under test.
     */
    private function getSut(?Closure $currentLanguageResolver = null): ResolveNavigationCacheKey
    {
        return new ResolveNavigationCacheKey(
            new FakeWpService([
                'addFilter' => true,
            ]),
            $currentLanguageResolver
        );
    }
}
