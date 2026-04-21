<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveHomeUrlTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveHomeUrl::class, $this->getSut());
    }

    #[TestDox('addHooks() registers Municipio home URL filter')]
    public function testAddHooksRegistersMunicipioHomeUrlFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolveHomeUrl($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/homeUrl', [$sut, 'resolveLanguageHomeUrl']]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('resolveLanguageHomeUrl() returns Polylang language home URL when available')]
    public function testResolveLanguageHomeUrlReturnsPolylangLanguageHomeUrl(): void
    {
        $sut = $this->getSut(
            languageHomeUrlResolver: static fn (): string => 'https://example.com/sv/'
        );

        static::assertSame('https://example.com/sv', $sut->resolveLanguageHomeUrl('https://example.com'));
    }

    #[TestDox('resolveLanguageHomeUrl() returns original URL when language home URL is unavailable')]
    public function testResolveLanguageHomeUrlReturnsOriginalUrlWhenLanguageHomeUrlIsUnavailable(): void
    {
        $sut = $this->getSut(
            languageHomeUrlResolver: static fn (): string => ''
        );

        static::assertSame('https://example.com', $sut->resolveLanguageHomeUrl('https://example.com'));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $languageHomeUrlResolver Optional language home URL resolver.
     *
     * @return ResolveHomeUrl The system under test.
     */
    private function getSut(?Closure $languageHomeUrlResolver = null): ResolveHomeUrl
    {
        return new ResolveHomeUrl(
            new FakeWpService([
                'addFilter' => true,
            ]),
            $languageHomeUrlResolver
        );
    }
}
