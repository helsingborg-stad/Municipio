<?php

declare(strict_types=1);

namespace Municipio\SearchIndex;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

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
        $feature = new SearchIndexFeature($wpService, new FakeAcfService(['getField' => false]));

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
        $feature = new SearchIndexFeature($wpService, new FakeAcfService(['getField' => false]));

        $feature->enable();

        $registeredHooks = array_map(static fn(array $arguments): string => $arguments[0], $wpService->methodCalls['addAction'] ?? []);
        static::assertNotContains('acf/init', $registeredHooks);
        static::assertNotEmpty($wpService->methodCalls['addFilter']);
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
}