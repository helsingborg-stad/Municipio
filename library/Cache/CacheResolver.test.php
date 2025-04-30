<?php

namespace Municipio\Cache;

use Municipio\Cache\Implementations\WpCache;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CacheResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $cacheResolver = new CacheResolver(new FakeWpService());
        $this->assertInstanceOf(CacheResolver::class, $cacheResolver);
    }

    /**
     * @testdox getCache() returns a CacheInterface instance
     */
    public function testGetCacheReturnsCacheInterfaceInstance()
    {
        $cacheResolver = new CacheResolver(new FakeWpService());
        $this->assertInstanceOf(CacheInterface::class, $cacheResolver->resolve());
    }
}
