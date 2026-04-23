<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveTranslatedPostTypeLinkTest extends TestCase
{
    #[TestDox('addHooks() registers the post_type_archive_link filter')]
    public function testAddHooksRegistersPostTypeArchiveLinkFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        $sut = new ResolveTranslatedPostTypeLink($wpService);

        $sut->addHooks();

        static::assertSame(
            [['post_type_archive_link', [$sut, 'resolveTranslatedPostTypeLink'], 10, 2]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('resolveTranslatedPostTypeLink() returns the translated permalink when a translated page exists')]
    public function testResolveTranslatedPostTypeLinkReturnsTranslatedPermalink(): void
    {
        $sut = $this->getSut(
            pageForPostType: 27,
            translatedPageId: 10739,
            permalink: 'https://example.com/en/books/'
        );

        static::assertSame(
            'https://example.com/en/books/',
            $sut->resolveTranslatedPostTypeLink('https://example.com/books/', 'books')
        );
    }

    #[TestDox('resolveTranslatedPostTypeLink() returns the original link when no page is assigned to the post type')]
    public function testResolveTranslatedPostTypeLinkReturnsOriginalLinkWhenNoPageAssigned(): void
    {
        $sut = $this->getSut(pageForPostType: false);

        static::assertSame(
            'https://example.com/books/',
            $sut->resolveTranslatedPostTypeLink('https://example.com/books/', 'books')
        );
    }

    #[TestDox('resolveTranslatedPostTypeLink() returns the original link when no translation exists')]
    public function testResolveTranslatedPostTypeLinkReturnsOriginalLinkWhenNoTranslationExists(): void
    {
        $sut = $this->getSut(
            pageForPostType: 27,
            translatedPageId: 0
        );

        static::assertSame(
            'https://example.com/books/',
            $sut->resolveTranslatedPostTypeLink('https://example.com/books/', 'books')
        );
    }

    /**
     * Get the system under test.
     *
     * @param mixed   $pageForPostType  The value returned by get_option('page_for_books').
     * @param int     $translatedPageId The translated page ID returned by the resolver.
     * @param string  $permalink        The permalink for the translated page.
     *
     * @return ResolveTranslatedPostTypeLink
     */
    private function getSut(
        mixed $pageForPostType = false,
        int $translatedPageId = 0,
        string $permalink = ''
    ): ResolveTranslatedPostTypeLink {
        return new ResolveTranslatedPostTypeLink(
            new FakeWpService([
                'addFilter'    => true,
                'getOption'    => $pageForPostType,
                'getPermalink' => $permalink ?: false,
            ]),
            static fn (int $pageId): int => $translatedPageId
        );
    }
}
