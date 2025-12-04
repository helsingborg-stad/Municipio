# ImageConvert

Municipio's on-demand image resizing system that creates images by exact pixel dimensions rather than predefined WordPress image sizes.

## Overview

ImageConvert provides a unique approach to image handling in WordPress by creating images on-demand using pixel dimensions instead of named sizes. This eliminates the need for predefined intermediate image sizes and image regeneration plugins.

### Basic Usage

Instead of using WordPress standard image sizes, you can request exact dimensions:

```php
// Create a 100x100 pixel image
wp_get_attachment_image_src($id, [100, 100]);

// Create a 300px wide image, preserving aspect ratio
wp_get_attachment_image_src($id, [300, false]);

// Create a 200px tall image, preserving aspect ratio  
wp_get_attachment_image_src($id, [false, 200]);
```

## Configuration

### Constants

#### MUNICIPIO_IMAGE_CONVERT_STRATEGY
Selects the image processing strategy. Currently only `runtime` is available.

```php
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'runtime');
```

**Available strategies:**
- `runtime` (default) - Process images immediately during page load

#### MUNICIPIO_IMAGE_CONVERT_DEFAULT_LOG_WRITER
Configures the default log writer for debugging and monitoring.

```php
define('MUNICIPIO_IMAGE_CONVERT_DEFAULT_LOG_WRITER', 'database');
```

**Available log writers:**
- `database` - Store logs in WordPress database
- `error_log` - Write to PHP error log

### Filters

All filters use the `Municipio/ImageConvert/` namespace prefix.

#### Core Configuration

**`Municipio/ImageConvert/IsEnabled`**
Enable or disable the ImageConvert system.
```php
add_filter('Municipio/ImageConvert/IsEnabled', '__return_false'); // Disable
```
*Default: true*

**`Municipio/ImageConvert/MaxImageDimension`**
Maximum allowed dimension for any image resize operation.
```php
add_filter('Municipio/ImageConvert/MaxImageDimension', function() {
    return 2560; // Allow up to 2560px images
});
```
*Default: 1920*

**`Municipio/ImageConvert/IntermidiateImageFormat`**
Target format for converted images.
```php
add_filter('Municipio/ImageConvert/IntermidiateImageFormat', function() {
    return 'jpg'; // Convert all images to JPG
});
```
*Default: 'webp'*
*Supported formats: webp, jpg, jpeg, png, gif, tiff*

**`Municipio/ImageConvert/IntermidiateImageQuality`**
Quality setting for image conversion (1-100).
```php
add_filter('Municipio/ImageConvert/IntermidiateImageQuality', function() {
    return 90; // Higher quality
});
```
*Default: 80*

#### File Handling

**`Municipio/ImageConvert/MimeTypes`**
Supported MIME types for image conversion.
```php
add_filter('Municipio/ImageConvert/MimeTypes', function($types) {
    $types[] = 'image/avif'; // Add AVIF support
    return $types;
});
```
*Default: image/jpeg, image/png, image/gif, image/tiff, image/webp*

**`Municipio/ImageConvert/MaxSourceFileSize`**
Maximum source file size in bytes that will be processed.
```php
add_filter('Municipio/ImageConvert/MaxSourceFileSize', function() {
    return 1024 * 1024 * 10; // 10MB limit
});
```
*Default: 5MB (5242880 bytes)*

#### Performance & Caching

**`Municipio/ImageConvert/FailedCacheExpiry`**
How long to cache failed conversion attempts (prevents retry loops).
```php
add_filter('Municipio/ImageConvert/FailedCacheExpiry', function() {
    return 3600; // Cache failures for 1 hour instead of 24 hours
});
```
*Default: 86400 (24 hours)*
*Purpose: Failed conversions are suspended for this duration to prevent repeated processing failures*

**`Municipio/ImageConvert/SuccessCacheExpiry`**
How long to cache successful conversion status.
```php
add_filter('Municipio/ImageConvert/SuccessCacheExpiry', function() {
    return 7 * 24 * 3600; // Cache success for 1 week
});
```
*Default: 86400 (24 hours)*

**`Municipio/ImageConvert/LockExpiry`**
Duration for conversion locks (prevents concurrent processing of same image).
```php
add_filter('Municipio/ImageConvert/LockExpiry', function() {
    return 600; // 10 minute lock timeout
});
```
*Default: 300 (5 minutes)*

**`Municipio/ImageConvert/PageCacheExpiry`**
Page-level cache duration for conversion status.
```php
add_filter('Municipio/ImageConvert/PageCacheExpiry', function() {
    return 1800; // 30 minutes
});
```
*Default: 3600 (1 hour)*

#### Advanced Configuration

**`Municipio/ImageConvert/ImageDownsizePriority`**
Priority for the image_downsize hook integration.
```php
add_filter('Municipio/ImageConvert/ImageDownsizePriority', function() {
    return 20; // Run after other plugins
});
```
*Default: 1*

**`Municipio/ImageConvert/InternalFilterPriority`**
Internal processing order for the image conversion pipeline.
```php
add_filter('Municipio/ImageConvert/InternalFilterPriority', function($priorities) {
    $priorities['normalizeImageSize'] = 15;
    return $priorities;
});
```

## How It Works

1. **Request Detection**: When `wp_get_attachment_image_src()` is called with array dimensions, ImageConvert intercepts the request
2. **Validation**: Checks if the source image exists, is valid, and within size limits  
3. **Cache Check**: Looks for existing converted image or failed conversion status
4. **Processing**: If needed, resizes the image using WordPress image editors (GD/Imagick)
5. **Caching**: Stores conversion status and locks to prevent duplicate processing
6. **Delivery**: Returns the converted image URL and dimensions

## Performance Considerations

### Memory Management
- Automatically increases memory limit to 2048M during processing
- Sets execution time limit to 300 seconds for large images

### File Size Limits
- Default maximum source file size: 5MB
- Larger files are rejected to prevent memory issues
- Configurable via `MaxSourceFileSize` filter

### Caching Strategy
- **Success Cache**: 24 hours - remembers successful conversions
- **Failure Cache**: 24 hours - prevents retry loops for failed conversions  
- **Lock Cache**: 5 minutes - prevents concurrent processing
- **Page Cache**: 1 hour - improves performance within single request

### Error Handling
- Failed conversions are suspended for 24 hours by default
- Comprehensive logging available for debugging
- Graceful fallback to original image if conversion fails

## Logging & Debugging

Enable logging by setting the log writer constant:

```php
define('MUNICIPIO_IMAGE_CONVERT_DEFAULT_LOG_WRITER', 'database');
```

Logs include:
- Successful conversions with timing information
- Failed conversions with error details
- Cache hit/miss statistics
- Lock acquisition and release events

## Troubleshooting

### Common Issues

**Images not converting:**
- Check if MIME type is supported via `MimeTypes` filter
- Verify source file size is within limits
- Check for failed conversion cache entries

**Memory errors:**
- Reduce `MaxSourceFileSize` limit
- Increase PHP memory_limit in server configuration
- Consider smaller source images

**Slow performance:**
- Enable object caching (Redis/Memcached) for production
- Adjust cache expiry times via filters
- Monitor conversion logs for bottlenecks

### Cache Management

Clear conversion caches if needed:
```php
// Clear all ImageConvert caches
wp_cache_flush();

// Or clear specific cache groups if your setup supports it
wp_cache_delete_group('municipio_image_convert');
```

## Production Recommendations

1. **Object Caching**: Use Redis or Memcached for optimal performance
2. **CDN Integration**: Serve converted images through CDN
3. **Monitoring**: Enable database logging to monitor conversion patterns
4. **Limits**: Set appropriate file size limits based on server resources
5. **Backup**: Ensure converted images are included in backup strategy