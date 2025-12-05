<?php

namespace Municipio\Helper\Memoize;

/**
 * Memoizes a callable function.
 */
class MemoizedFunction
{
    private array $cache = [];
    private $fn;
    private $cacheKeyGenerator;

    /**
     * @param callable $fn The function to memoize.
     */
    public function __construct(callable $fn, ?callable $cacheKeyGenerator = null)
    {
        $this->fn                = $fn;
        $this->cacheKeyGenerator = $cacheKeyGenerator ?? fn(...$args) => md5(serialize($args));
    }

    /**
     * Invokes the memoized function with the given arguments.
     */
    public function __invoke(...$args)
    {
        $key = ($this->cacheKeyGenerator)(...$args);
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = ($this->fn)(...$args);
        }
        return $this->cache[$key];
    }
}
