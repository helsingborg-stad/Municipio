<?php

namespace Municipio\Cache\MethodCache;

use Municipio\Cache\GlobalCache;
use ReflectionObject;
use ReflectionProperty;

trait MethodCacheTrait
{
    /**
     * Caches a function call
     *
     * @param callable  $callable   The function call to cache.
     * @param array     $arguments  Argument sent function call.
     * @param int|null  $expire     Expiration time in seconds or null for no expiration.
     * @param array     $mode       Cache mode. Can be 'global' or 'object'. This determines if cache key should account for global variables
     *                              and/or object properties. Default is ['global', 'object'].
     *
     * @return mixed The result of the function call.
     */
    public function cache(callable $callable, array $args, ?int $expire = null, bool|array $useGlobalState = false): mixed
    {
        $cacheKey   = $this->getKey($args, $useGlobalState);
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
     * @param bool|array $useGlobalState Whether to use global state in the cache key.
     * @return string The cache key.
     */
    private function getKey(array $args, bool|array $useGlobalState): string
    {
        if ($useGlobalState !== false) {
            return
                $this->hash($args) .
                $this->getRelevantGlobalsIdentifier($useGlobalState) .
                $this->getRelevantObjectPropertiesIdentifier($this);
        }

        return $this->hash($args) . $this->getRelevantObjectPropertiesIdentifier($this);
    }

    /**
     * Returns the relevant global variables to be used in the cache key.
     *
     * @return array The relevant global variables.
     */
    private function getRelevantGlobals(array $globalKeysToInclude): array
    {
        if (empty($globalKeysToInclude)) {
            return array_filter($GLOBALS, fn ($key) =>  !in_array($key, $this->getDefaultExcludedFromGlobals()), ARRAY_FILTER_USE_KEY);
        }

        return array_filter($GLOBALS, fn ($key) =>  in_array($key, $globalKeysToInclude), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Returns the default excluded global variables.
     *
     * @return array The default excluded global variables.
     */
    private function getDefaultExcludedFromGlobals(): array
    {
        // TODO: apply filter
        return [
            '__composer_autoload_files',
            'wp_cache',
            '_SERVER',
            '_GET',
            '_POST',
            '_COOKIE',
            '_FILES',
            '_REQUEST',
            '_SESSION',
            '_ENV'
        ];
    }

    /**
     * Returns a identifier of the relevant global variables.
     *
     * @return string Identifier
     */
    private function getRelevantGlobalsIdentifier(true|array $globalKeysToInclude): string
    {
        $globalKeysToInclude = $globalKeysToInclude === true ? [] : $globalKeysToInclude;
        return $this->hash($this->getRelevantGlobals($globalKeysToInclude));
    }

    /**
     * Returns the relevant object properties to be used in the cache key.
     *
     * @param object $object The object to get properties from.
     * @return string Identifier
     */
    private function getRelevantObjectPropertiesIdentifier($object): string
    {
        if (method_exists($object, '__toString')) {
            return $object->__toString();
        }
        return $this->hash($this->getObjectProperties($object));
    }

    /**
     * Returns a identifier of the object.
     *
     * @param object $object The object to get details from.
     * @return string Identifier
     *
     * Notes: We considered to use serialize intstead of json_encode, but it is not
     * as fast as json_encode. Json encode may however generate different hashes for
     * different objects with the same properties due to the order of the properties.
     * If analyze shows that many missing hashes are generated, we can consider
     * to use serialize instead.
     */
    private function hash(string|int|float|bool|array|object $item): string
    {
        return (string) crc32(json_encode($item));
    }

    /**
     * Returns the properties of an object.
     *
     * @param object $object The object to get properties from.
     * @return array The properties of the object.
     *
     * Notes: We considered both reflection and native mode (get_object_vars). Reflection should be slower, but tests have shown
     * that it is actually faster.
     */
    private function getObjectProperties($object): array
    {
        $reflection = new ReflectionObject($object);
        $properties = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $property->setAccessible(true);
            $properties[$property->getName()] = $property->getValue($object);
        }

        return $properties;
    }
}
