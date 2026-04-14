<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Query;
use WpService\Implementations\FakeWpService;

class ResolveFontAttachmentQueriesTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveFontAttachmentQueries::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the pre_get_posts action')]
    public function testAddHooksRegistersPreGetPostsAction(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
        ]);

        $sut = new ResolveFontAttachmentQueries($wpService);

        $sut->addHooks();

        static::assertSame('pre_get_posts', $wpService->methodCalls['addAction'][0][0]);
        static::assertSame(1, $wpService->methodCalls['addAction'][0][2]);
    }

    #[TestDox('makeFontAttachmentQueryLanguageAgnostic() updates font attachment queries with array mime types')]
    public function testMakeFontAttachmentQueryLanguageAgnosticUpdatesArrayMimeTypeQuery(): void
    {
        $query = new WP_Query();
        $query->query_vars = [
            'post_type'      => 'attachment',
            'post_mime_type' => ['application/font-woff', 'font/woff2'],
        ];

        $sut = $this->getSut(
            polylangIsActiveResolver: static fn (): bool => true
        );

        $sut->makeFontAttachmentQueryLanguageAgnostic($query);

        static::assertSame('', $query->get('lang'));
        static::assertTrue($query->get('suppress_filters'));
    }

    #[TestDox('makeFontAttachmentQueryLanguageAgnostic() updates font attachment queries with string mime types')]
    public function testMakeFontAttachmentQueryLanguageAgnosticUpdatesStringMimeTypeQuery(): void
    {
        $query = new WP_Query();
        $query->query_vars = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'application/font-woff',
        ];

        $sut = $this->getSut(
            polylangIsActiveResolver: static fn (): bool => true
        );

        $sut->makeFontAttachmentQueryLanguageAgnostic($query);

        static::assertSame('', $query->get('lang'));
        static::assertTrue($query->get('suppress_filters'));
    }

    #[TestDox('makeFontAttachmentQueryLanguageAgnostic() does not update non-font attachment queries')]
    public function testMakeFontAttachmentQueryLanguageAgnosticDoesNotUpdateNonFontQueries(): void
    {
        $query = new WP_Query();
        $query->query_vars = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image/jpeg',
        ];

        $sut = $this->getSut(
            polylangIsActiveResolver: static fn (): bool => true
        );

        $sut->makeFontAttachmentQueryLanguageAgnostic($query);

        static::assertNull($query->get('lang'));
        static::assertNull($query->get('suppress_filters'));
    }

    #[TestDox('makeFontAttachmentQueryLanguageAgnostic() does not update queries when Polylang is unavailable')]
    public function testMakeFontAttachmentQueryLanguageAgnosticDoesNotUpdateQueriesWhenPolylangIsUnavailable(): void
    {
        $query = new WP_Query();
        $query->query_vars = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'application/font-woff',
        ];

        $sut = $this->getSut(
            polylangIsActiveResolver: static fn (): bool => false
        );

        $sut->makeFontAttachmentQueryLanguageAgnostic($query);

        static::assertNull($query->get('lang'));
        static::assertNull($query->get('suppress_filters'));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $polylangIsActiveResolver Optional Polylang availability resolver.
     *
     * @return ResolveFontAttachmentQueries The system under test.
     */
    private function getSut(?Closure $polylangIsActiveResolver = null): ResolveFontAttachmentQueries
    {
        return new ResolveFontAttachmentQueries(
            new FakeWpService([
                'addAction' => true,
            ]),
            $polylangIsActiveResolver
        );
    }
}
