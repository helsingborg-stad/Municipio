# Bugfix: Typesense Provider error_log Type Error

## Issue
Error in `algolia-index-typesense-provider` plugin:
```
PHP Fatal error: Uncaught TypeError: error_log(): Argument #1 ($message) must be of type string, int given
in /var/www/prod/wp-content/plugins/algolia-index-typesense-provider/source/php/Provider/Typesense/TypesenseProvider.php:177
```

## Root Cause
The `error_log()` function expects a string parameter, but `$response['statusCode']` is an integer.

## Fix
Convert the status code to a string using a type cast:

**File:** `source/php/Provider/Typesense/TypesenseProvider.php`
**Line:** 177

**Before:**
```php
error_log($response['statusCode']);
```

**After:**
```php
error_log((string) $response['statusCode']);
```

## Repository
The fix should be applied to: https://github.com/helsingborg-stad/algolia-index-typesense-provider

## Patch
The patch has been created and is available in the algolia-index-typesense-provider repository on branch `fix/error-log-type-conversion`.
