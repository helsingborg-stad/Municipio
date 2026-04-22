<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveNavigationFetchUrlLanguageTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveNavigationFetchUrlLanguage::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the page tree fetch URL filter')]
    public function testAddHooksRegistersPageTreeFetchUrlFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        $sut = new ResolveNavigationFetchUrlLanguage($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Navigation/PageTree/FetchUrl', [$sut, 'appendCurrentLanguage'], 10, 4]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('appendCurrentLanguage() appends the current language to fetch URLs')]
    public function testAppendCurrentLanguageAppendsCurrentLanguageToFetchUrl(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv'
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/wp-json/municipio/v1/navigation/children/render?pageId=26&depth=2&identifier=mobile&lang=sv',
            $sut->appendCurrentLanguage('http://localhost:8080/hbgcom/wp-json/municipio/v1/navigation/children/render?pageId=26&depth=2&identifier=mobile')
        );
    }

    #[TestDox('appendCurrentLanguage() leaves fetch URLs with an existing language unchanged')]
    public function testAppendCurrentLanguageLeavesExistingLanguageUnchanged(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv'
        );

        static::assertSame(
            'http://localhost:8080/hbgcom/wp-json/municipio/v1/navigation/children/render?pageId=26&depth=2&identifier=mobile&lang=en',
            $sut->appendCurrentLanguage('http://localhost:8080/hbgcom/wp-json/municipio/v1/navigation/children/render?pageId=26&depth=2&identifier=mobile&lang=en')
        );
    }

    #[TestDox('appendCurrentLanguage() falls back to the request lang when current language is unavailable')]
    public function testAppendCurrentLanguageFallsBackToRequestLang(): void
    {
        $previousLang = $_GET['lang'] ?? null;
        $_GET['lang'] = 'sv';

        try {
            $sut = $this->getSut();

            static::assertSame(
                'http://localhost:8080/hbgcom/wp-json/municipio/v1/navigation/children/render?pageId=26&depth=2&identifier=mobile&lang=sv',
                $sut->appendCurrentLanguage('http://localhost:8080/hbgcom/wp-json/municipio/v1/navigation/children/render?pageId=26&depth=2&identifier=mobile')
            );
        } finally {
            if ($previousLang === null) {
                unset($_GET['lang']);
            } else {
                $_GET['lang'] = $previousLang;
            }
        }
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     *
     * @return ResolveNavigationFetchUrlLanguage
     */
    private function getSut(?Closure $currentLanguageResolver = null): ResolveNavigationFetchUrlLanguage
    {
        return new ResolveNavigationFetchUrlLanguage(
            new FakeWpService(['addFilter' => true]),
            $currentLanguageResolver
        );
    }
}