<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Migration;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests deactivation of legacy plugin behavior that conflicts with SearchIndex.
 */
class LegacyPluginConflictGuardTest extends TestCase
{
    /**
     * Verify active legacy plugins are deactivated and the admin notice is registered.
     */
    public function testDeactivatesActiveLegacyPlugins(): void
    {
        $wpService = new FakeWpService([
            'isPluginActive' => static fn(string $plugin): bool => in_array($plugin, [
                'algolia-index/algolia-index.php',
                'algolia-index-js-searchpage-addon/algolia-index-js-searchpage.php',
                'algolia-index-modularity-addon/algolia-index-modularity-addon.php',
                'algolia-index-attachments/algolia-add-attachment-to-index.php',
            ], true),
            'isPluginActiveForNetwork' => false,
            'deactivatePlugins' => null,
            'addAction' => true,
            'updateOption' => true,
        ]);

        $wasDeactivated = (new LegacyPluginConflictGuard($wpService))->deactivateConflictingPlugins();

        static::assertTrue($wasDeactivated);
        static::assertSame([
            'algolia-index/algolia-index.php',
            'algolia-index-js-searchpage-addon/algolia-index-js-searchpage.php',
            'algolia-index-modularity-addon/algolia-index-modularity-addon.php',
            'algolia-index-attachments/algolia-add-attachment-to-index.php',
        ], $wpService->methodCalls['deactivatePlugins'][0][0]);
        static::assertSame('municipio_search_index_legacy_search_page_was_active', $wpService->methodCalls['updateOption'][0][0]);
        static::assertSame('municipio_search_index_legacy_attachments_was_active', $wpService->methodCalls['updateOption'][1][0]);
        static::assertSame('admin_notices', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * Verify no deactivation occurs when legacy plugins are inactive.
     */
    public function testDoesNothingWhenLegacyPluginsAreInactive(): void
    {
        $wpService = new FakeWpService([
            'isPluginActive' => false,
            'isPluginActiveForNetwork' => false,
        ]);

        $wasDeactivated = (new LegacyPluginConflictGuard($wpService))->deactivateConflictingPlugins();

        static::assertFalse($wasDeactivated);
        static::assertArrayNotHasKey('deactivatePlugins', $wpService->methodCalls);
    }
}