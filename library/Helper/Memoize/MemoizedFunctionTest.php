<?php

namespace Municipio\Helper\Memoize;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MemoizedFunctionTest extends TestCase
{
    #[TestDox('memoizes results')]
    public function testMemoization()
    {
        $calls            = 0;
        $memoizedFunction = new MemoizedFunction(function ($x) use (&$calls) {
            $calls++;
            return $x * 2;
        });

        $this->assertEquals(4, $memoizedFunction(2));
        $this->assertEquals(4, $memoizedFunction(2));
        $this->assertEquals(6, $memoizedFunction(3));
        $this->assertEquals(4, $memoizedFunction(2));
        $this->assertEquals(2, $calls);
    }

    #[TestDox('memoizes results with custom cache key')]
    public function testCustomCacheKey()
    {
        $calls            = 0;
        $memoizedFunction = new MemoizedFunction(function ($x) use (&$calls) {
            $calls++;
            return $x * 2;
        }, fn($x) => "custom_key_{$x}");

        $this->assertEquals(4, $memoizedFunction(2));
        $this->assertEquals(4, $memoizedFunction(2));
        $this->assertEquals(6, $memoizedFunction(3));
        $this->assertEquals(4, $memoizedFunction(2));
        $this->assertEquals(2, $calls);
    }

    #[TestDox('static cache key returns the same result for all calls')]
    public function testCustomCacheKeyWithDifferentArgumentsSameKey()
    {
        $calls            = 0;
        $memoizedFunction = new MemoizedFunction(function ($x) use (&$calls) {
            $calls++;
            return $x * 2;
        }, fn($x) => "custom_key");

        $this->assertEquals(2, $memoizedFunction(1));
        $this->assertEquals(2, $memoizedFunction(2));
        $this->assertEquals(2, $memoizedFunction(3));
        $this->assertEquals(1, $calls);
    }
}
