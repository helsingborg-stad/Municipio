<?php

namespace Municipio\Customizer\Applicators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use wpdb;
use WpService\Implementations\FakeWpService;

function is_admin(): bool
{
    return false;
}

class ApplicatorCacheTest extends TestCase
{
    protected function tearDown(): void
    {
        $tracker = new \ReflectionProperty(ApplicatorCache::class, 'firstRunTracker');
        $tracker->setAccessible(true);
        $tracker->setValue(null, []);
    }

    #[TestDox('tryCreateAndApplyCache applies applicator data without using option cache')]
    public function testTryCreateAndApplyCacheAppliesApplicatorDataWithoutUsingOptionCache(): void
    {
        $wpService = new FakeWpService();
        $wpdb = $this->createMock(wpdb::class);
        $applicator = new class() implements ApplicatorInterface {
            public array $appliedData = [];

            public function getKey(): string
            {
                return 'test-applicator';
            }

            public function getData(): array|object|string
            {
                return ['color' => 'blue'];
            }

            public function applyData(array|object $data)
            {
                $this->appliedData[] = $data;
            }
        };

        $sut = new ApplicatorCache($wpService, $wpdb, $applicator);

        $sut->tryCreateAndApplyCache();

        $this->assertSame([['color' => 'blue']], $applicator->appliedData);
        $this->assertArrayNotHasKey('getOption', $wpService->methodCalls);
        $this->assertArrayNotHasKey('addOption', $wpService->methodCalls);
    }

    #[TestDox('addHooks only registers direct output hooks')]
    public function testAddHooksOnlyRegistersDirectOutputHooks(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $wpdb = $this->createMock(wpdb::class);
        $applicator = new class() implements ApplicatorInterface {
            public function getKey(): string
            {
                return 'test-applicator';
            }

            public function getData(): array|object|string
            {
                return [];
            }

            public function applyData(array|object $data)
            {
                return;
            }
        };

        $sut = new ApplicatorCache($wpService, $wpdb, $applicator);

        $sut->addHooks();

        $this->assertCount(2, $wpService->methodCalls['addAction']);
        $this->assertSame('kirki_dynamic_css', $wpService->methodCalls['addAction'][0][0]);
        $this->assertSame('rest_api_init', $wpService->methodCalls['addAction'][1][0]);
    }
}
