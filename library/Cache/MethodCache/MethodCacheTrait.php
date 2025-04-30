<?php

namespace Municipio\Cache\MethodCache;

use Municipio\Cache\GlobalCache;

trait MethodCacheTrait
{
    /**
     * Caches a function call
     *
     * @param callable $callable  The function call to cache.
     * @param array $arguments to function call.
     */
    public function cache(callable $callable, array $args, ?int $expire = null)
    {
        $cacheKey   = $this->serializeArgs($args);
        $cacheGroup = $this->serializeCallable($callable);
        $cached     = GlobalCache::getCache()->get($cacheKey, $cacheGroup);

        if ($cached !== null) {
            return $cached;
        }

        $value = $this->call($callable, $args);
        GlobalCache::getCache()->set($cacheKey, $value, $cacheGroup, $expire);
        return $value;
    }

    /**
     * Calls the callable with the provided arguments.
     *
     * @param callable $callable The callable to call.
     * @param array $args The arguments to pass to the callable.
     *
     * @return mixed The result of the callable.
     */
    private function call(callable $callable, array $args): mixed
    {
        return call_user_func($callable, ...$args);
    }

    /**
     * Serializes the callable to a string.
     *
     * @param callable $callable The callable to serialize.
     * @return string The serialized callable.
     */
    private function serializeCallable(callable $callable): string
    {
        $callableId = match (true) {
            is_string($callable) => $callable,
            is_array($callable) && is_object($callable[0]) => spl_object_hash($callable[0]) . '::' . $callable[1],
            is_array($callable) => $callable[0] . '::' . $callable[1],
            default => throw new \InvalidArgumentException('Unsupported callable.'),
        };

        return md5($callableId);
    }

    /**
     * Serializes the arguments to a string.
     *
     * @param array $args The arguments to serialize.
     * @return string The serialized arguments.
     */
    private function serializeArgs(array $args): string
    {
        return
            serialize($this->getRelevantGlobals()) .
            serialize(get_object_vars($this)) .
            serialize($args);
    }

    /**
     * Returns the relevant global variables to be used in the cache key.
     *
     * @return array The relevant global variables.
     */
    private function getRelevantGlobals(): array
    {
        return array_filter($GLOBALS, function ($key) {
            return !in_array($key, ['_SERVER', '_GET', '_POST', '_ENV', '__composer_autoload_files']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
