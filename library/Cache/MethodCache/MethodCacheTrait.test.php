<?php

namespace Municipio\Cache\MethodCache;

use Municipio\Cache\GlobalCache;
use Municipio\Cache\Implementations\StaticCache;
use PHPUnit\Framework\TestCase;

class MethodCacheTraitTest extends TestCase implements MethodCacheInterface
{
    use MethodCacheTrait;

    public string $string = '';

    protected function setUp(): void
    {
        parent::setUp();
        GlobalCache::setCache(new StaticCache());
    }

    /**
     * @test cache() returns the same value for the same arguments, while only invoking the method once.
     */
    public function testCacheReturnsSameValue()
    {
        $this->assertEquals('test', $this->cache([$this, 'getSameStringAsProvided'], ['test']));
        $this->assertEquals('test', $this->cache([$this, 'getSameStringAsProvided'], ['test'])); // Throws if not returned from cache
    }

    /**
     * @testdox cache() does not return cached value if function signature changes by changing value of global variable.
     */
    public function testCacheAccountsForGlobalStateChange()
    {
        $GLOBALS['cacheTestGlobal'] = 'foo';
        $this->assertEquals('foo', $this->cache([$this, 'getGlobalValue'], [], null, ['cacheTestGlobal']));

        $GLOBALS['cacheTestGlobal'] = 'bar';
        $this->assertEquals('bar', $this->cache([$this, 'getGlobalValue'], [], null, ['cacheTestGlobal']));
    }

    public function testCacheAccountsForClassVariableChange()
    {
        $this->string = 'foo';
        $this->assertEquals('foo', $this->cache([$this, 'getStringVariableFromClass'], []));

        $this->string = 'bar';
        $this->assertEquals('bar', $this->cache([$this, 'getStringVariableFromClass'], []));
    }

    /**
     * @testdox performance meets expectations of 50% faster than uncached.
     * @dataProvider performanceDataProvider
     */
    public function testPerformance(float $result): void
    {
        $this->assertGreaterThan(50, $result, 'Caching mechanism is not 50% faster than uncached.');
    }

    public function performanceDataProvider(): array
    {
        GlobalCache::setCache(new StaticCache());
        $timesToRun  = 10000;
        $cachedStart = microtime(true);

        for ($i = 0; $i < $timesToRun; $i++) {
            $this->cache([$this, 'getSameStringAsProvided'], ['testcached']);
        }

        $cachedEnd           = microtime(true);
        $cachedExecutionTime = $cachedEnd - $cachedStart;

        $uncachedStart = microtime(true);

        for ($i = 0; $i < $timesToRun; $i++) {
            $this->getSameStringAsProvided('testuncached-' . $i);
        }

        $uncachedEnd             = microtime(true);
        $uncachedExecutionTime   = $uncachedEnd - $uncachedStart;
        $performanceInPercentage = ($uncachedExecutionTime - $cachedExecutionTime) / $uncachedExecutionTime * 100;

        return [
            "{$performanceInPercentage}" => [
                $performanceInPercentage,
            ],
        ];
    }

    private function getSameStringAsProvided(string $string): string
    {
        static $methodCalls = [];

        if (in_array($string, $methodCalls)) {
            throw new \Exception('Method called more than one with the same argument');
        }

        $methodCalls[] = $string;

        return $string;
    }

    private function getGlobalValue(): string
    {
        return $GLOBALS['cacheTestGlobal'];
    }

    private function getStringVariableFromClass(): string
    {
        return $this->string;
    }
}
