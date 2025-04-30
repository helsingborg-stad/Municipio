<?php

namespace Municipio\Cache\Implementations;

use PHPUnit\Framework\TestCase;

class StaticCacheTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clear the static cache after each test
        $cache = new StaticCache();
        $cache->flush();
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cache = new StaticCache();
        $this->assertInstanceOf(StaticCache::class, $cache);
    }

    /**
     * @testdox allows to set and retrieve values
     */
    public function testGetReturnsValueIfSet()
    {
        $cache = new StaticCache();
        $cache->set('key', 'value');

        $this->assertEquals('value', $cache->get('key'));
    }

    /**
     * @testdox allows to set and retrieve values with default group
     */
    public function testGetReturnsValueWithDefaultGroup()
    {
        $cache = new StaticCache();
        $cache->set('key', 'value');

        $this->assertEquals('value', $cache->get('key', StaticCache::DEFAULT_GROUP));
    }

    /**
     * @testdox allows to set and retrieve values with specified group
     */
    public function testGetReturnsValueWithGroup()
    {
        $cache = new StaticCache();
        $cache->set('key', 'value', 'group1');

        $this->assertEquals('value', $cache->get('key', 'group1'));
        $this->assertNull($cache->get('key', 'group2'));
    }

    /**
     * @testdox get() returns null if not set
     */
    public function testGetReturnsNullIfNotSet()
    {
        $cache = new StaticCache();
        $this->assertNull($cache->get('non_existent_key', 'group'));
    }

    /**
     * @testdox delete() removes the value
     */
    public function testDeleteRemovesValue()
    {
        $cache = new StaticCache();
        $cache->set('key', 'value', 'group');
        $cache->delete('key', 'group');

        $this->assertNull($cache->get('key', 'group'));
    }

    /**
     * @testdox delete() removes the value without group
     */
    public function testDeleteRemovesValueWithoutGroup()
    {
        $cache = new StaticCache();
        $cache->set('key', 'value');
        $cache->delete('key');

        $this->assertNull($cache->get('key'));
    }
}
