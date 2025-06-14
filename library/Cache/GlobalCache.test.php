<?php

namespace Municipio\Cache;

use Municipio\Cache\Implementations\NullCache;
use PHPUnit\Framework\TestCase;

class GlobalCacheTest extends TestCase
{
    /**
     * @testdox getCache() returns a CacheInterface instance
     */
    public function testGetCacheReturnsCacheInterfaceInstance()
    {
        GlobalCache::setCache(new NullCache());
        $this->assertInstanceOf(CacheInterface::class, GlobalCache::getCache());
    }

    /**
     * @testdox getCache() throws exception if not set
     * @runInSeparateProcess
     */
    public function testGetCacheThrowsExceptionIfNotSet()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cache instance is not set.');
        GlobalCache::getCache();
    }
}
