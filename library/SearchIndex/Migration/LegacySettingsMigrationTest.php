<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Migration;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests migration from legacy Algolia Index plugin settings.
 */
class LegacySettingsMigrationTest extends TestCase
{
    /**
     * Verify legacy Typesense settings, facets, and selection migrate once.
     */
    public function testMigratesTypesenseSettingsAndPreservesExistingValues(): void
    {
        $values = [
            'algolia_index_search_provider' => 'typesense',
            'algolia_index_typesense_api_url' => 'https://typesense.example.test',
            'algolia_index_typesense_api_key' => implode('-', ['typesense', 'server', 'key']),
            'algolia_index_typesense_collection_name' => 'legacy-content',
            'algolia_index_facetting' => [[
                'attribute' => 'categories',
                'label' => 'Categories',
                'enabled' => true,
            ]],
            'search_index_name' => 'existing-index-name',
        ];
        $acfService = new FakeAcfService([
            'getField' => static function (string $field) use (&$values): mixed {
                return $values[$field] ?? false;
            },
            'updateField' => static function (string $field, mixed $value) use (&$values): bool {
                $values[$field] = $value;
                return true;
            },
        ]);
        $wpService = new FakeWpService([
            'getOption' => static fn(string $option, mixed $default): mixed => $option === 'algolia_index' ? [] : $default,
            'updateOption' => true,
        ]);

        (new LegacySettingsMigration($wpService, $acfService))->migrate();

        static::assertSame('typesense', $values['search_index_provider']);
        static::assertSame('https://typesense.example.test', $values['search_index_typesense_api_url']);
        static::assertSame('legacy-content', $values['search_index_typesense_collection_name']);
        static::assertSame('existing-index-name', $values['search_index_name']);
        static::assertSame($values['algolia_index_facetting'], $values['search_index_facets']);
        static::assertSame('municipio_search_index_legacy_settings_migrated', $wpService->methodCalls['updateOption'][0][0]);
    }

    /**
     * Verify legacy pre-ACF Algolia options are migrated as a fallback.
     */
    public function testMigratesLegacyAlgoliaOptionFallback(): void
    {
        $values = [];
        $acfService = new FakeAcfService([
            'getField' => static function (string $field) use (&$values): mixed {
                return $values[$field] ?? false;
            },
            'updateField' => static function (string $field, mixed $value) use (&$values): bool {
                $values[$field] = $value;
                return true;
            },
        ]);
        $wpService = new FakeWpService([
            'getOption' => static fn(string $option, mixed $default): mixed => match ($option) {
                'algolia_index' => [
                    'application_id' => 'legacy-application-id',
                    'api_key' => implode('-', ['legacy', 'api', 'key']),
                    'index_name' => 'legacy-content',
                ],
                default => $default,
            },
            'updateOption' => true,
        ]);

        (new LegacySettingsMigration($wpService, $acfService))->migrate();

        static::assertSame('legacy-application-id', $values['search_index_algolia_application_id']);
        static::assertSame('algolia', $values['search_index_provider']);
        static::assertSame('legacy-content', $values['search_index_name']);
    }
}