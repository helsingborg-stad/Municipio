# Minimal Async Attributes for Small URLs

## Problem

Previously, `getAsyncAttributes` was sending large amounts of data through URL parameters, including:
- Full `$this->data` array from the controller
- `wpTaxonomies` (global taxonomies object)
- `customizer` settings
- `archiveProps` (archive configuration)
- Various other properties

This resulted in extremely long URLs that could hit browser/server limits and were inefficient.

## Solution

Implemented a **minimal attributes + backend reconstruction** approach:

1. **Store only essential identifiers in the URL** (minimal data)
2. **Reconstruct full data on the backend** when async requests come in

### Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│ Archive.php (Initial Page Load)                                │
├─────────────────────────────────────────────────────────────────┤
│ 1. Create minimal async attributes:                            │
│    - postType: 'post'                                          │
│    - queryVarsPrefix: 'archive_'                               │
│    - archivePropsKey: 'archivePost'                            │
│                                                                 │
│ 2. Pass to AsyncConfigBuilderFactory                           │
│                                                                 │
│ 3. SourceAttributesExtractor filters (allowlist):              │
│    - Only keeps: postType, queryVarsPrefix, archivePropsKey    │
│    - Excludes: wpTaxonomies, customizer, archiveProps, etc.    │
│                                                                 │
│ 4. Small attributes sent in URL                                │
└─────────────────────────────────────────────────────────────────┘
                              ↓
                         [URL with minimal params]
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ PostsListRender.php (Async Request Handler)                    │
├─────────────────────────────────────────────────────────────────┤
│ 1. Receive minimal attributes from URL                         │
│                                                                 │
│ 2. Detect minimal mode (has 'archivePropsKey')                 │
│                                                                 │
│ 3. AsyncAttributesReconstructor::enrich()                      │
│    - Query WordPress for customizer settings                   │
│    - Get wpTaxonomies from globals                             │
│    - Reconstruct archiveProps from customizer                  │
│                                                                 │
│ 4. Pass enriched data to ConfigMapper                          │
│                                                                 │
│ 5. Render posts list with full configuration                   │
└─────────────────────────────────────────────────────────────────┘
```

### Key Components

#### 1. Archive.php
Creates minimal async attributes instead of full data:
```php
$minimalAsyncAttributes = [
    'postType' => $this->data['postType'] ?? 'page',
    'queryVarsPrefix' => 'archive_',
    'archivePropsKey' => 'archive' . $this->camelCasePostTypeName($this->data['postType'] ?? 'page'),
];
```

#### 2. SourceAttributesExtractor
Uses **allowlist approach** to filter attributes:
```php
private const ALLOWED_KEYS = [
    'postType',
    'queryVarsPrefix',
    'archivePropsKey',
];
```

Only these keys are kept, everything else is filtered out.

#### 3. AsyncAttributesReconstructor (NEW)
Reconstructs full data from minimal identifiers:
```php
$enriched = AsyncAttributesReconstructor::enrich($attributes, $wpService);
// Now $enriched contains:
// - Original minimal attributes
// - wpTaxonomies (queried from globals)
// - customizer (queried from WordPress)
// - archiveProps (extracted from customizer)
// - wpService, wpdb
```

#### 4. PostsListRender.php
Detects minimal mode and reconstructs:
```php
if (isset($attributes['archivePropsKey'])) {
    $attributes = AsyncAttributesReconstructor::enrich($attributes, $wpService);
}
```

### Benefits

1. **Minimal URL size** - Only 3 small string values instead of large objects
2. **No data limit issues** - Won't hit browser/server URL limits
3. **Fresh data** - Backend queries ensure up-to-date customizer/taxonomy data
4. **Maintainable** - Clear separation between URL transport and data reconstruction

### Backward Compatibility

The system is designed to work alongside any existing code:
- If attributes contain `archivePropsKey`, they're treated as minimal and reconstructed
- If attributes don't have `archivePropsKey`, they pass through unchanged
- Tests updated to reflect the new minimal behavior

### Testing

Tests verify:
1. Minimal attributes contain only essential identifiers
2. Large objects are filtered out (archiveProps, wpTaxonomies, etc.)
3. Reconstructor can rebuild full data from minimal identifiers
4. PostsListRender correctly detects and enriches minimal attributes

### Example URL Comparison

**Before (verbose):**
```
/wp-json/municipio/v1/posts-list/render?attributes={"postType":"post","queryVarsPrefix":"archive_","wpTaxonomies":{...1000+ chars...},"customizer":{...500+ chars...},"archiveProps":{...800+ chars...},"numberOfColumns":3,"postsPerPage":10,...}
```

**After (ultra-minimal):**
```
/wp-json/municipio/v1/posts-list/render?attributes={"postType":"post","queryVarsPrefix":"archive_","archivePropsKey":"archivePost"}
```

The new URL contains **ONLY 3 values** - all configuration data is reconstructed on the backend from these identifiers.
