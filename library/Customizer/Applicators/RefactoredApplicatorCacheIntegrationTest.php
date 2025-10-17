<?php

namespace Municipio\Customizer\Applicators;

use PHPUnit\Framework\TestCase;
use WpService\WpService;
use wpdb;
use Municipio\Customizer\Applicators\Cache\CacheComponentFactory;

/**
 * Integration test for the refactored applicator cache
 */
class RefactoredApplicatorCacheIntegrationTest extends TestCase
{
    private $wpService;
    private $wpdb;
    private $applicators;
    private $refactoredCache;

    protected function setUp(): void
    {
        $this->wpService = $this->createMock(WpService::class);
        $this->wpdb = $this->createMock(wpdb::class);
        $this->wpdb->options = 'wp_options';

        // Create mock applicators
        $this->applicators = [
            $this->createMock(ApplicatorInterface::class),
            $this->createMock(ApplicatorInterface::class)
        ];

        $this->applicators[0]->method('getKey')->willReturn('test_applicator_1');
        $this->applicators[0]->method('getData')->willReturn(['data' => 'test1']);

        $this->applicators[1]->method('getKey')->willReturn('test_applicator_2');
        $this->applicators[1]->method('getData')->willReturn(['data' => 'test2']);

        // Create factory and cache manager
        $factory = new CacheComponentFactory($this->wpService, $this->wpdb);
        $cacheManager = $factory->createCacheManager();

        $this->refactoredCache = new RefactoredApplicatorCache(
            $this->wpService,
            $cacheManager,
            ...$this->applicators
        );
    }

    public function testRefactoredCacheCanBeConstructed(): void
    {
        $this->assertInstanceOf(RefactoredApplicatorCache::class, $this->refactoredCache);
        $this->assertInstanceOf(ApplicatorCacheInterface::class, $this->refactoredCache);
    }

    public function testRefactoredCacheImplementsHookable(): void
    {
        $this->assertInstanceOf(\Municipio\HooksRegistrar\Hookable::class, $this->refactoredCache);
    }

    public function testClearCacheReturnsBool(): void
    {
        $this->wpdb->method('get_col')->willReturn([]);
        
        $result = $this->refactoredCache->tryClearCache();
        
        $this->assertIsBool($result);
    }

    public function testHooksCanBeAdded(): void
    {
        $this->wpService->expects($this->atLeastOnce())->method('addAction');
        
        $this->refactoredCache->addHooks();
    }
}