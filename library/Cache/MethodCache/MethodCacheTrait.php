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
            $this->getRelevantGlobalsIdentifier() .
            $this->getRelevantObjectPropertiesIdentifier($this) .
            $this->hash($args);
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

    /**
     * Returns a identifier of the relevant global variables.
     *
     * @return string Identifier
     */
    private function getRelevantGlobalsIdentifier(): string
    {
        return $this->hash($this->getRelevantGlobals());
    }

    /**
     * Returns the relevant object properties to be used in the cache key.
     *
     * @param object $object The object to get properties from.
     * @return string Identifier
     */
    private function getRelevantObjectPropertiesIdentifier($object): string
    {
        if(method_exists($object, '__toString')) {
            return $object->__toString();
        }
        return $this->hash($this->getObjectProperties($object, 'reflection'));
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
     * @param string $mode The mode to use for getting properties ('reflection' or 'native').
     * @return array The properties of the object.
     * 
     * Notes: We cosidered both reflection and native mode. Reflection should be slower, but tests have shown
     * that it is actually faster.
     * 
     * Reflection: Get a score of appriximately 70%.
     * Native: Get a score of appriximately 50%.
     */
    private function getObjectProperties($object, $mode = 'reflection'): array
    {
        if ($mode === 'reflection') {
            $reflection = new ReflectionObject($object);
            $properties = [];

            foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $property->setAccessible(true);
                $properties[$property->getName()] = $property->getValue($object);
            }

            return $properties;
        }

        if($mode === 'native') {
            return get_object_vars($object);
        }
        
        throw new \InvalidArgumentException('Unsupported mode. Use "reflection" or "native".');
    }
}