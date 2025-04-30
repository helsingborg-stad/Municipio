<?php

namespace Municipio\Cache\Implementations;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class WpCacheTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cache = new WpCache(new FakeWpService());
        $this->assertInstanceOf(WpCache::class, $cache);
    }

    /**
     * @testdox get() returns result from wpService
     */
    public function testGetReturnsResultFromWpService()
    {
        $cache = new WpCache(new FakeWpService(['wpCacheGet' => 'expected_value']));
        $this->assertEquals('expected_value', $cache->get('test_key'));
    }

    /**
     * @testdox set() calls wpService with correct parameters
     */
    public function testSetCallsWpService()
    {
        $wpService = new FakeWpService(['wpCacheSet' => true]);
        $cache     = new WpCache($wpService);

        $cache->set('test_key', 'test_value', 'test_group', 3600);

        $this->assertEquals('test_key', $wpService->methodCalls['wpCacheSet'][0][0]);
        $this->assertEquals('test_value', $wpService->methodCalls['wpCacheSet'][0][1]);
        $this->assertEquals('test_group', $wpService->methodCalls['wpCacheSet'][0][2]);
        $this->assertEquals(3600, $wpService->methodCalls['wpCacheSet'][0][3]);
    }

    /**
     * @testdox delete() calls wpService with correct parameters
     */
    public function testDeleteCallsWpService()
    {
        $wpService = new FakeWpService(['wpCacheDelete' => true]);
        $cache     = new WpCache($wpService);

        $cache->delete('test_key', 'test_group');

        $this->assertEquals('test_key', $wpService->methodCalls['wpCacheDelete'][0][0]);
        $this->assertEquals('test_group', $wpService->methodCalls['wpCacheDelete'][0][1]);
    }
}
