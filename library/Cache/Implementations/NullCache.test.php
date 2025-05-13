<?php

namespace Municipio\Cache\Implementations;

use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cache = new NullCache();
        $this->assertInstanceOf(NullCache::class, $cache);
    }

    /**
     * @testdox get() returns null
     */
    public function testGetReturnsNull()
    {
        $cache = new NullCache();
        $cache->set('key', 'value');

        $this->assertNull($cache->get('key'));
    }
}
