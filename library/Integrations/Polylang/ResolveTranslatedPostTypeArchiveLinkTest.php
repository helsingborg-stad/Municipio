<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveTranslatedPostTypeArchiveLinkTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(
            ResolveTranslatedPostTypeArchiveLink::class,
            $this->getSut()
        );
    }

    #[TestDox('addHooks() registers the post_type_archive_link filter')]
    public function testAddHooksRegistersFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $sut       = new ResolveTranslatedPostTypeArchiveLink($wpService);

        $sut->addHooks();

        static::assertSame(
            [['post_type_archive_link', [$sut, 'resolveTranslatedArchiveLink'], 10, 2]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() rebuilds the URL using the translated page URI and language home URL')]
    public function testResolveTranslatedArchiveLinkRebuildsUrl(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageUri: 'shop',
            translatedPostResolver: static fn (int $id): int => $id === 42 ? 100 : 0,
            languageHomeUrlResolver: static fn (): string => 'https://example.test/en'
        );

        static::assertSame(
            'https://example.test/en/shop/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product')
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when no page is mapped')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenNoMapping(): void
    {
        $sut = $this->getSut(
            getOption: false,
            getPageUri: 'shop',
            translatedPostResolver: static fn (int $id): int => 100
        );

        static::assertSame(
            'https://example.test/product/',
            $sut->resolveTranslatedArchiveLink('https://example.test/product/', 'product')
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when the post type is empty')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenPostTypeEmpty(): void
    {
        $sut = $this->getSut();

        static::assertSame(
            'https://example.test/product/',
            $sut->resolveTranslatedArchiveLink('https://example.test/product/', '')
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when the translated page ID equals the stored ID')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenTranslationSameAsStored(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageUri: 'butik',
            translatedPostResolver: static fn (int $id): int => 42,
            languageHomeUrlResolver: static fn (): string => 'https://example.test/sv'
        );

        static::assertSame(
            'https://example.test/butik/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product')
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when the translation cannot be resolved')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenNoTranslation(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageUri: 'butik',
            translatedPostResolver: static fn (int $id): int => 0
        );

        static::assertSame(
            'https://example.test/butik/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product')
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when Polylang is not active')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenPolylangInactive(): void
    {
        $sut = $this->getSut(getOption: 42, getPageUri: 'shop');

        static::assertSame(
            'https://example.test/butik/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product')
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when the language home URL resolver is unavailable')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenNoLanguageHomeUrl(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageUri: 'shop',
            translatedPostResolver: static fn (int $id): int => 100
        );

        static::assertSame(
            'https://example.test/butik/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product')
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when the page URI is empty')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenPageUriEmpty(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageUri: '',
            translatedPostResolver: static fn (int $id): int => 100,
            languageHomeUrlResolver: static fn (): string => 'https://example.test/en'
        );

        static::assertSame(
            'https://example.test/butik/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product')
        );
    }

    /**
     * Get the system under test.
     */
    private function getSut(
        mixed $getOption = false,
        string $getPageUri = '',
        ?Closure $translatedPostResolver = null,
        ?Closure $languageHomeUrlResolver = null
    ): ResolveTranslatedPostTypeArchiveLink {
        $wpService = new FakeWpService([
            'addFilter'  => true,
            'getOption'  => $getOption,
            'getPageUri' => $getPageUri,
        ]);

        return new ResolveTranslatedPostTypeArchiveLink(
            $wpService,
            $translatedPostResolver,
            $languageHomeUrlResolver
        );
    }
}
