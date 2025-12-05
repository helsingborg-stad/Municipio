# Table of Contents Feature

The Table of Contents (TOC) feature provides automatic generation and display of a navigable table of contents for posts and pages with headings.

## Architecture

This feature follows the same architectural pattern as the MirroredPost feature, using SOLID and DRY principles:

### Components

1. **TocFeature** - Main feature coordinator that enables the functionality
2. **TocPostObject** - PostObject decorator that adds TOC functionality 
3. **TocUtils** - Utility class for determining when to enable TOC and business logic
4. **TableOfContents** - Core utility for parsing HTML and generating TOC data

### Integration Pattern

The feature integrates with the PostObject system using the decorator pattern:

1. `TocFeature` is enabled in `App.php` 
2. It registers a filter on `CreatePostObjectFromWpPost::DECORATE_FILTER_NAME`
3. When posts are processed, `TocPostObject` decorates eligible posts
4. The decorator provides TOC data and content with anchor IDs
5. Views access TOC data through PostObject properties

### Key Features

- **Automatic Detection**: Only enables for singular pages with headings
- **Content Enhancement**: Injects anchor IDs into headings for navigation
- **Nested Structure**: Builds hierarchical TOC based on heading levels
- **Backwards Compatibility**: Provides magic method access to TOC properties
- **Filtered Content Integration**: Works with WordPress-filtered content

### Usage in Views

```blade
@if (!empty($post->tableOfContents))
    {{-- Display table of contents --}}
@endif

{{-- Content automatically includes anchor IDs when TOC is enabled --}}
{!! $post->postContentFiltered !!}
```

### Properties Available

- `$post->tableOfContents` - Array of TOC items with nested structure
- `$post->hasTableOfContents` - Boolean indicating if TOC exists
- `$post->postContentFiltered` - Content with anchor IDs injected (when TOC enabled)
- `$post->documentWithAnchors` - Alias for content with anchors

## Configuration

The feature is automatically enabled for:
- Singular pages/posts (`is_singular()`)
- Content containing HTML headings (h1-h6)

No additional configuration is required - the feature works out of the box.