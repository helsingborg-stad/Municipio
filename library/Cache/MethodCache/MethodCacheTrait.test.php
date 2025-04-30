<?php

namespace Municipio\Cache\MethodCache;

use Municipio\Cache\GlobalCache;
use Municipio\Cache\Implementations\StaticCache;
use PHPUnit\Framework\TestCase;

class MethodCacheTraitTest extends TestCase
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
        $this->assertEquals('foo', $this->cache([$this, 'getGlobalValue'], []));

        $GLOBALS['cacheTestGlobal'] = 'bar';
        $this->assertEquals('bar', $this->cache([$this, 'getGlobalValue'], []));
    }

    public function testCacheAccountsForClassVariableChange()
    {
        $this->string = 'foo';
        $this->assertEquals('foo', $this->cache([$this, 'getStringVariableFromClass'], []));

        $this->string = 'bar';
        $this->assertEquals('bar', $this->cache([$this, 'getStringVariableFromClass'], []));
    }

    /**
     * @testdox cache() is fast enough to be used in a loop.
     * @runInSeparateProcess
     */
    public function testPerformance()
    {
        $timesToRun = 10000;
        $start      = microtime(true);

        for ($i = 0; $i < $timesToRun; $i++) {
            $this->cache([$this, 'getSameStringAsProvided'], ['test-' . $i]);
        }

        $end           = microtime(true);
        $executionTime = $end - $start;
        $this->assertLessThan(1, $executionTime, 'Caching mechanism is too slow.');
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
