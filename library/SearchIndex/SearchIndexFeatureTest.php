<?php

declare(strict_types=1);

namespace Municipio\SearchIndex;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\AdminNotices\AdminNoticesInterface;
use Municipio\Helper\AdminNotices\AdminNoticeType;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManager;

/**
 * Tests SearchIndex initialization relative to the ACF lifecycle.
 */
class SearchIndexFeatureTest extends TestCase
{
    /**
     * Verify initialization is deferred when ACF has not started.
     */
    public function testDefersInitializationUntilAcfInit(): void
    {
        $wpService = $this->createWpService(0);
        $feature = new SearchIndexFeature(
            $wpService,
            new FakeAcfService(['getField' => false]),
            new EnqueueManager($wpService),
            static::createNullAdminNoticesService()
        );

        $feature->enable();

        static::assertSame('acf/init', $wpService->methodCalls['addAction'][0][0]);
        static::assertSame(20, $wpService->methodCalls['addAction'][0][2]);
    }

    /**
     * Verify initialization runs immediately when ACF has already fired.
     */
    public function testInitializesImmediatelyAfterAcfInit(): void
    {
        $wpService = $this->createWpService(1);
        $feature = new SearchIndexFeature(
            $wpService,
            new FakeAcfService(['getField' => false]),
            new EnqueueManager($wpService),
            static::createNullAdminNoticesService()
        );

        $feature->enable();

        $registeredHooks = array_map(static fn(array $arguments): string => $arguments[0], $wpService->methodCalls['addAction'] ?? []);
        static::assertNotContains('acf/init', $registeredHooks);
        static::assertNotEmpty($wpService->methodCalls['addFilter']);
    }

    /**
     * Verify the legacy attachment add-on immediately enables PDF indexing.
     */
    public function testEnablesPdfIndexingWhenLegacyAttachmentPluginIsActive(): void
    {
        $options = [];
        $wpService = new FakeWpService([
            'isPluginActive' => static fn(string $plugin): bool => $plugin === 'algolia-index-attachments/algolia-add-attachment-to-index.php',
            'isPluginActiveForNetwork' => false,
            'updateOption' => static function (string $option, mixed $value) use (&$options): bool {
                $options[$option] = $value;
                return true;
            },
            'getOption' => static function (string $option, mixed $default) use (&$options): mixed {
                return $options[$option] ?? $default;
            },
            'deactivatePlugins' => null,
            'addAction' => true,
        ]);
        $acfService = new FakeAcfService(['getField' => false, 'updateField' => true]);
        $feature = new SearchIndexFeature(
            $wpService,
            $acfService,
            new EnqueueManager($wpService),
            static::createNullAdminNoticesService()
        );

        $feature->enable();

        static::assertSame(['application/pdf'], $acfService->methodCalls['updateField'][0][1]);
        static::assertFalse($options['municipio_search_index_legacy_attachments_was_active']);
    }

    /**
     * Create a WordPress service fake for SearchIndex lifecycle setup.
     */
    private function createWpService(int $acfInitCount): FakeWpService
    {
        return new FakeWpService([
            'didAction' => $acfInitCount,
            'isPluginActive' => false,
            'isPluginActiveForNetwork' => false,
            'addAction' => true,
            'addFilter' => true,
        ]);
    }

    private static function createNullAdminNoticesService(): AdminNoticesInterface {
        return new class implements AdminNoticesInterface {
            public function addNotice(string $message, AdminNoticeType $type = AdminNoticeType::INFO, bool $dismissible = true): void
            {
            }
        };
    }
}