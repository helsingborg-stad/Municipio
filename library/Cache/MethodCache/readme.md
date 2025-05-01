# Method Cache Trait Documentation

## Overview

This feature provides a caching mechanism for function calls using the `MethodCacheTrait`. It allows you to cache function results based on function arguments, relevant global variables, and object properties. The cache key is generated dynamically based on a set of modes (`'global'`, `'object'`, or both). This ensures that the cache key changes depending on the input parameters and object state.

## Key Concepts

1. **Modes**: Control the generation of the cache key based on global variables, object properties, or both.
2. **Cache Key Generation**: The cache key is dynamically generated using global variables, object properties, and function arguments.
3. **`__toString()` Method**: An optional method that can be implemented in objects to customize the cache key generation based on object properties.

## Methods Overview

### `cache(callable $callable, array $args, ?int $expire = null, $mode = ['global', 'object'])`

Caches a function call. This method uses the `serializeArgs` and `serializeCallable` methods to generate the cache key. The cache key can be influenced by global variables and object properties based on the provided mode.

**Parameters:**
- `$callable`: The function or method to be cached.
- `$args`: Arguments to pass to the callable.
- `$expire`: Expiration time in seconds or null for no expiration.
- `$mode`: Array specifying cache modes. Can be `['global']`, `['object']`, or both `['global', 'object']`.

**Returns:**
- The result of the function call or the cached value if it exists.

---

### `serializeArgs(array $args, $mode)`

Serializes the function arguments into a string, appending global and object property identifiers based on the provided mode.

**Parameters:**
- `$args`: Arguments for the function call.
- `$mode`: Array specifying cache modes (`'global'`, `'object'`).

**Returns:**
- A string that represents the serialized arguments, global variables, and object properties.

---

### `getRelevantGlobalsIdentifier()`

Generates a cache key based on the relevant global variables. Global variables that are excluded are: `_SERVER`, `_GET`, `_POST`, `_ENV`, and `__composer_autoload_files`.

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

### `__toString()`

If the object has a `__toString()` method, it will be used to generate the cache key. This allows for a custom cache key based on object properties.

---

## Modes

### 1. **Global Mode**

In **Global Mode**, the cache key includes global variables, excluding system variables like `_SERVER`, `_POST`, `_GET`, etc. This mode is useful when caching depends on the global environment (e.g., session data or request parameters).

```php
$mode = ['global']; // Use global variables to generate cache key
$args = ['param1', 'param2'];
$cacheResult = $this->cache($callableFunction, $args, null, $mode);
```

### 2. **Object Mode**

In **Object Mode**, the cache key includes the object's public properties. If the object implements the `__toString()` method, it will be used to generate the cache key. This mode is useful when the cache result depends on the object's state.

```php
$mode = ['object']; // Use object properties to generate cache key
$args = ['param1', 'param2'];
$object = new MyClass('value1', 'value2');
$cacheResult = $this->cache([$object, 'methodName'], $args, null, $mode);
```

### 3. **Both Global and Object Modes**

You can combine both **Global Mode** and **Object Mode** by passing both modes in the array. This is useful when both the global environment and object properties should influence the cache key.

```php
$mode = ['global', 'object']; // Use both global variables and object properties for cache key
$args = ['param1', 'param2'];
$object = new MyClass('value1', 'value2');
$cacheResult = $this->cache([$object, 'methodName'], $args, null, $mode);
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
$mode = ['object']; // Use object properties (via __toString) for cache key
$args = ['param1', 'param2'];
$cacheResult = $this->cache([$object, 'methodName'], $args, null, $mode);
```

## Example Usages

### Example 1: Cache with Global Mode Only

```php
$mode = ['global']; // Only use global variables in the cache key
$args = ['some', 'arguments'];

$cacheResult = $this->cache($callableFunction, $args, null, $mode);
```

### Example 2: Cache with Object Mode Only

```php
$mode = ['object']; // Only use object properties in the cache key
$args = ['some', 'arguments'];

$object = new MyClass('value1', 'value2');
$cacheResult = $this->cache([$object, 'methodName'], $args, null, $mode);
```

### Example 3: Cache with Both Global and Object Modes

```php
$mode = ['global', 'object']; // Use both global variables and object properties in the cache key
$args = ['some', 'arguments'];

$object = new MyClass('value1', 'value2');
$cacheResult = $this->cache([$object, 'methodName'], $args, null, $mode);
```

## Conclusion

This caching system offers flexible and efficient caching with dynamically generated cache keys based on global variables, object properties, or both. The `__toString()` method can be used to further customize the cache key based on object state. This feature improves performance by avoiding redundant calculations and storing function results for reuse.
