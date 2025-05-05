# Method Cache Trait Documentation

## Overview

This feature provides a caching mechanism for function calls using the `MethodCacheTrait`. It allows you to cache function results based on function arguments, relevant global variables, and object properties. The cache key is generated dynamically based on a set of modes (`'global'`, `'object'`, or both). This ensures that the cache key changes depending on the input parameters and object state.

## Key Concepts

1. **Modes**: Control the generation of the cache key based on global variables, object properties, or both.
2. **Cache Key Generation**: The cache key is dynamically generated using global variables, object properties, and function arguments.
3. **`__toString()` Method**: An optional method that can be implemented in objects to customize the cache key generation based on object properties.

## Methods Overview

### `cache(callable $callable, array $args, ?int $expire = null, bool|array $useGlobalState = false)`

Caches a function call. This method uses the `getKey` and `serializeCallable` methods to generate the cache key. The cache key can be influenced by global variables and object properties based on the provided mode.

**Parameters:**
- `$callable`: The function or method to be cached.
- `$args`: Arguments to pass to the callable.
- `$expire`: Expiration time in seconds or null for no expiration.
- `$useGlobalState`: Boolean or array specifying whether to include global variables in the cache key. If `true`, all globals are included except excluded ones. If an array, only specified global keys are included.

**Returns:**
- The result of the function call or the cached value if it exists.

---

### `getKey(array $args, bool|array $useGlobalState)`

Generates the cache key by serializing the function arguments, relevant global variables, and object properties.

**Parameters:**
- `$args`: Arguments for the function call.
- `$useGlobalState`: Boolean or array specifying whether to include global variables in the cache key.

**Returns:**
- A string representing the cache key.

---

### `serializeCallable(callable $callable)`

Serializes the callable into a string to be used as part of the cache key.

**Parameters:**
- `$callable`: The callable to serialize.

**Returns:**
- A string representing the serialized callable.

---

### `getRelevantGlobalsIdentifier(bool|array $globalKeysToInclude)`

Generates a cache key based on the relevant global variables. If `$globalKeysToInclude` is `true`, all globals except excluded ones are included. If it is an array, only specified global keys are included.

**Parameters:**
- `$globalKeysToInclude`: Boolean or array specifying which global variables to include.

**Returns:**
- A string that identifies relevant global variables.

---

### `getRelevantObjectPropertiesIdentifier($object)`

Generates a cache key based on the object's public properties. If the object implements the `__toString()` method, it will use that method to generate the identifier.

**Parameters:**
- `$object`: The object to retrieve properties from.

**Returns:**
- A string representing the object's identifier.

---

### `hash(string|int|float|bool|array|object $item)`

Hashes the given item into a string using `crc32` and `json_encode`.

**Parameters:**
- `$item`: The item to hash.

**Returns:**
- A string representing the hash of the item.

---

### `getObjectProperties($object)`

Retrieves the public properties of an object using reflection.

**Parameters:**
- `$object`: The object to retrieve properties from.

**Returns:**
- An array of the object's public properties.

---

## Use Global State

### 1. **Use Global State**

In **Use Global State**, the cache key includes global variables, excluding system variables like `_SERVER`, `_POST`, `_GET`, etc. This mode is useful when caching depends on the global environment (e.g., session data or request parameters). An array of global variable names can be passed to the cache method to include them in the cache key.

```php
$args = ['param1', 'param2'];
$cacheResult = $this->cache($callableFunction, $args, null, ['post']);
```

## Using the `__toString()` Method for Cache ID

The `__toString()` method can be implemented in the object to return a custom string representation of the object. This string can be used as part of the cache key, helping to uniquely identify the object in the cache.

### Example of `__toString()`:

```php
class MyClass {
    public $property1;
    public $property2;

    public function __construct($property1, $property2) {
        $this->property1 = $property1;
        $this->property2 = $property2;
    }

    public function __toString(): string {
        // Generate a custom cache key based on the object properties
        return $this->property1 . '-' . $this->property2;
    }
}
```

When the `__toString()` method is available, it will be called to generate the cache key. For instance, if `MyClass` is used in caching, the cache key will be `"value1-value2"` based on the values of `property1` and `property2`.

```php
$object = new MyClass('value1', 'value2');
$args = ['param1', 'param2'];
$cacheResult = $this->cache([$object, 'methodName'], $args, null);
```

## Conclusion

This caching system offers flexible and efficient caching with dynamically generated cache keys based on global variables, object properties, or both. The `__toString()` method can be used to further customize the cache key based on object state. This feature improves performance by avoiding redundant calculations and storing function results for reuse.
