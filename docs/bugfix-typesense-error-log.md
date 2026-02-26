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
The patch has been created and is available in this repository at:
`docs/algolia-index-typesense-provider-error-log-fix.patch`

### How to Apply the Patch

1. Navigate to the algolia-index-typesense-provider plugin directory:
   ```bash
   cd path/to/algolia-index-typesense-provider
   ```

2. Apply the patch:
   ```bash
   git apply /path/to/algolia-index-typesense-provider-error-log-fix.patch
   ```

3. Or apply directly from this repository:
   ```bash
   cd path/to/algolia-index-typesense-provider
   curl https://raw.githubusercontent.com/helsingborg-stad/Municipio/copilot/bugfix-index-to-typesense/docs/algolia-index-typesense-provider-error-log-fix.patch | git apply
   ```

### Alternative: Manual Fix
Simply change line 177 in `source/php/Provider/Typesense/TypesenseProvider.php` from:
```php
error_log($response['statusCode']);
```
to:
```php
error_log((string) $response['statusCode']);
```
