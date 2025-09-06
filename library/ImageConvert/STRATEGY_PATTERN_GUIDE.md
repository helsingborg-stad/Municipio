# Image Conversion Strategy Pattern

The ImageConvert functionality now supports a strategy pattern that allows you to choose between different image conversion approaches based on your specific needs.

## Available Strategies

### 1. Runtime Strategy (Default)
- **Identifier**: `runtime`
- **Behavior**: Processes images immediately during the request
- **Use Case**: When you need immediate image conversion and can tolerate potential page load delays
- **Performance**: Immediate results but may slow down page loads for large images

### 2. Background Strategy
- **Identifier**: `background`
- **Behavior**: Queues images for background processing via WordPress cron
- **Use Case**: When you prioritize fast page loads over immediate image availability
- **Performance**: No impact on page load times, but converted images appear after background processing

## Configuration

### Setting the Strategy

Define the `MUNICIPIO_IMAGE_CONVERT_STRATEGY` constant in your `wp-config.php` or theme configuration:

```php
// For runtime processing (default)
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'runtime');

// For background processing
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'background');
```

### Example Configuration

```php
// wp-config.php
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'background');

// Optional: Customize cache expiration times
add_filter('municipio_image_convert_failed_cache_expiry', function($seconds) {
    return 7200; // 2 hours instead of default 1 hour
});

add_filter('municipio_image_convert_success_cache_expiry', function($seconds) {
    return 172800; // 48 hours instead of default 24 hours
});
```

## Action Namespace Compliance

The background strategy conforms to the action namespace `Municipio/ImageConvert/Convert` as requested:

```php
// The background strategy triggers this action for each conversion
do_action('Municipio/ImageConvert/Convert', [
    'image_id' => $imageId,
    'width' => $width,
    'height' => $height,
    'format' => $format,
    'original_url' => $originalUrl,
    'original_path' => $originalPath,
    'intermediate_location' => $intermediateLocation
]);
```

## Page Load Optimization

The new system includes page load caching to prevent multiple image generations within the same request:

### Features
- **Request-level deduplication**: Prevents processing the same image multiple times in one request
- **Page load tracking**: Checks if a page was loaded after the image's last modification
- **Runtime caching**: Fast in-memory caching for the current request
- **Persistent caching**: Cross-process safety using WordPress cache

### Benefits
- Eliminates redundant processing during page loads
- Reduces server load during initial page rendering
- Prevents race conditions when multiple images are processed simultaneously

## Monitoring and Debugging

### Error Logging
All strategies log conversion attempts and errors:

```php
// Example log entries
Image conversion error for Image ID: 123. Page: /example-page/. Strategy: runtime. Message: File not found
Background conversion requested for Image ID: 456, 800x600, format: webp
```

### Cache Monitoring
Monitor cache performance using WordPress object cache statistics:

```php
// Check conversion status
$cache = new ConversionCache($wpService);
$hasRecentFailure = $cache->hasRecentFailure($imageId, $width, $height, 'webp');

// Check page load optimization
$pageCache = new PageLoadCache($wpService);
$wasProcessedInRequest = $pageCache->hasBeenProcessedInCurrentRequest($imageId, $width, $height, 'webp');
```

## Migration from Previous Implementation

The new strategy pattern is **fully backward compatible**. Existing installations will:

1. Continue using the runtime strategy by default
2. Maintain all existing functionality
3. Benefit from improved caching and deduplication
4. Require no code changes

To migrate to background processing:

1. Add the strategy constant to your configuration
2. Ensure WordPress cron is working properly
3. Monitor the conversion queue and error logs
4. Optionally customize cache expiration times

## Performance Expectations

### Runtime Strategy
- **Page Load Impact**: Potentially slower for large images
- **Image Availability**: Immediate
- **Server Resources**: Higher during page loads
- **Cache Benefits**: Full caching and deduplication apply

### Background Strategy  
- **Page Load Impact**: Minimal (original images served immediately)
- **Image Availability**: After background processing (typically 1-60 minutes)
- **Server Resources**: Distributed over time
- **Cache Benefits**: Full caching plus queue management

## Extending the Strategy Pattern

The pattern is designed for future extension. To add new strategies:

1. Implement the `ConversionStrategyInterface`
2. Add the strategy to `StrategyFactory::getSupportedStrategies()`
3. Update the strategy creation logic in `StrategyFactory::createStrategy()`

```php
class CustomConversionStrategy implements ConversionStrategyInterface
{
    public function convert(ImageContract $image, string $format): ImageContract|false
    {
        // Your custom conversion logic
        return $image;
    }

    public function canHandle(ImageContract $image, string $format): bool
    {
        return true; // Or your specific conditions
    }

    public function getName(): string
    {
        return 'custom';
    }
}
```

## Troubleshooting

### Common Issues

1. **Background processing not working**
   - Verify WordPress cron is enabled: `wp config get DISABLE_WP_CRON`
   - Check for scheduled events: `wp cron event list`
   - Monitor error logs for queue processing issues

2. **Images not converting**
   - Check file permissions on upload directories
   - Verify image editor availability (GD or Imagick)
   - Review error logs for specific conversion failures

3. **Performance issues**
   - Monitor cache hit rates
   - Consider adjusting cache expiration times
   - Review server resource usage during peak times

### Debug Mode

Enable debug logging by adding to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Then monitor `/wp-content/debug.log` for detailed conversion information.