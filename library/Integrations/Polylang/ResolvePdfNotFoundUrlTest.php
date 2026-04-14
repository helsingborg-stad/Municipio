<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolvePdfNotFoundUrlTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolvePdfNotFoundUrl::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the PDF not-found URL filter')]
    public function testAddHooksRegistersPdfNotFoundUrlFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolvePdfNotFoundUrl($wpService);

        $sut->addHooks();

        static::assertSame(
            ['Municipio/Pdf/NotFoundUrl'],
            array_column($wpService->methodCalls['addFilter'], 0)
        );
    }

    #[TestDox('resolveNotFoundUrl() returns a language-aware 404 URL when Polylang resolves a home URL')]
    public function testResolveNotFoundUrlReturnsLanguageAware404Url(): void
    {
        $sut = $this->getSut(
            languageHomeUrlResolver: static fn (): string => 'https://example.com/sv/'
        );

        static::assertSame('https://example.com/sv/404', $sut->resolveNotFoundUrl('https://example.com/404'));
    }

    #[TestDox('resolveNotFoundUrl() returns the original URL when no language home URL is available')]
    public function testResolveNotFoundUrlReturnsOriginalUrlWhenNoLanguageHomeUrlIsAvailable(): void
    {
        $sut = $this->getSut(
            languageHomeUrlResolver: static fn (): string => ''
        );

        static::assertSame('https://example.com/404', $sut->resolveNotFoundUrl('https://example.com/404'));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $languageHomeUrlResolver Optional language home URL resolver.
     *
     * @return ResolvePdfNotFoundUrl The system under test.
     */
    private function getSut(?Closure $languageHomeUrlResolver = null): ResolvePdfNotFoundUrl
    {
        return new ResolvePdfNotFoundUrl(
            new FakeWpService([
                'addFilter' => true,
            ]),
            $languageHomeUrlResolver
        );
    }
}
