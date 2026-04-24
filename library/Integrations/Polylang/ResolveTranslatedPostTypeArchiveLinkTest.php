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
            $this->getSut(),
        );
    }

    #[TestDox('addHooks() registers the post_type_archive_link filter')]
    public function testAddHooksRegistersFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $sut = new ResolveTranslatedPostTypeArchiveLink($wpService);

        $sut->addHooks();

        static::assertSame(
            [['post_type_archive_link', [$sut, 'resolveTranslatedArchiveLink'], 10, 2]],
            $wpService->methodCalls['addFilter'],
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() delegates to get_page_link() for the translated page so ancestor slugs are translated')]
    public function testResolveTranslatedArchiveLinkDelegatesToGetPageLink(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageLink: 'https://example.test/en/visit-experience/food-drinks/',
            translatedPostResolver: static fn(int $id): int => $id === 42 ? 100 : 0,
        );

        static::assertSame(
            'https://example.test/en/visit-experience/food-drinks/',
            $sut->resolveTranslatedArchiveLink('https://example.test/en/besoka-uppleva/food-drinks/', 'food-drinks'),
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() falls back to the stored page ID when the translation resolver returns 0')]
    public function testResolveTranslatedArchiveLinkFallsBackToStoredIdWhenNoTranslation(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageLink: 'https://example.test/sv/besoka-uppleva/mat-och-dryck/',
            translatedPostResolver: static fn(int $id): int => 0,
        );

        static::assertSame(
            'https://example.test/sv/besoka-uppleva/mat-och-dryck/',
            $sut->resolveTranslatedArchiveLink('https://example.test/original/', 'food-drinks'),
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when no page is mapped')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenNoMapping(): void
    {
        $sut = $this->getSut(
            getOption: false,
            getPageLink: 'https://example.test/should/not/be/used/',
            translatedPostResolver: static fn(int $id): int => 100,
        );

        static::assertSame(
            'https://example.test/product/',
            $sut->resolveTranslatedArchiveLink('https://example.test/product/', 'product'),
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when the post type is empty')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenPostTypeEmpty(): void
    {
        $sut = $this->getSut();

        static::assertSame(
            'https://example.test/product/',
            $sut->resolveTranslatedArchiveLink('https://example.test/product/', ''),
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when Polylang is not active')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenPolylangInactive(): void
    {
        $sut = $this->getSut(getOption: 42, getPageLink: 'https://example.test/should/not/be/used/');

        static::assertSame(
            'https://example.test/butik/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product'),
        );
    }

    #[TestDox('resolveTranslatedArchiveLink() returns the original link when get_page_link() returns an empty string')]
    public function testResolveTranslatedArchiveLinkReturnsOriginalWhenPageLinkEmpty(): void
    {
        $sut = $this->getSut(
            getOption: 42,
            getPageLink: '',
            translatedPostResolver: static fn(int $id): int => 100,
        );

        static::assertSame(
            'https://example.test/butik/',
            $sut->resolveTranslatedArchiveLink('https://example.test/butik/', 'product'),
        );
    }

    /**
     * Get the system under test.
     */
    private function getSut(
        mixed $getOption = false,
        string $getPageLink = '',
        ?Closure $translatedPostResolver = null,
    ): ResolveTranslatedPostTypeArchiveLink {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getOption' => $getOption,
            'getPageLink' => $getPageLink,
        ]);

        return new ResolveTranslatedPostTypeArchiveLink(
            $wpService,
            $translatedPostResolver,
        );
    }
}
