<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Provider\Typesense;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests Typesense HTTP provider behavior without a live Typesense service.
 */
class TypesenseProviderTest extends TestCase
{
    /**
     * Verify that search requests are normalized to the shared provider response format.
     */
    public function testSearchReturnsDocumentHits(): void
    {
        $requestedUrl = '';
        $wpService = new FakeWpService([
            'wpRemoteRequest' => static function (string $url) use (&$requestedUrl): array {
                $requestedUrl = $url;
                return [];
            },
            'isWpError' => false,
            'wpRemoteRetrieveBody' => json_encode([
                'hits' => [['document' => ['ID' => '42', 'post_title' => 'Search result']]],
            ], JSON_THROW_ON_ERROR),
            'wpRemoteRetrieveResponseCode' => 200,
        ]);
        $provider = new TypesenseProvider(
            $wpService,
            implode('-', ['typesense', 'server', 'key']),
            'https://typesense.example.test',
            'municipio-content',
        );

        $result = $provider->search('municipio search');

        static::assertSame([
            ['ID' => '42', 'post_title' => 'Search result'],
        ], $result['hits']);
        static::assertSame(
            'https://typesense.example.test/collections/municipio-content/documents/search?q=municipio+search&query_by=post_title%2Cpost_excerpt%2Ccontent&page=1&per_page=20',
            $requestedUrl,
        );
    }

    /**
     * Verify that deleting a document that has not been indexed is a no-op.
     */
    public function testDeleteObjectIgnoresMissingDocument(): void
    {
        $wpService = new FakeWpService([
            'wpRemoteRequest' => [],
            'isWpError' => false,
            'wpRemoteRetrieveBody' => json_encode([
                'message' => 'Could not find a document with id: missing-document',
            ], JSON_THROW_ON_ERROR),
            'wpRemoteRetrieveResponseCode' => 404,
        ]);
        $provider = new TypesenseProvider(
            $wpService,
            implode('-', ['typesense', 'server', 'key']),
            'https://typesense.example.test',
            'municipio-content',
        );

        static::assertNull($provider->deleteObject('missing-document'));
    }
}