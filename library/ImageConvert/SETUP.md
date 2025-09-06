# Image Conversion Strategy Setup Guide

This guide provides comprehensive setup instructions for Municipio's image conversion strategies. The system supports three different strategies, each optimized for specific use cases and performance requirements.

## Overview

The ImageConvert system uses a **strategy pattern** to provide flexible image conversion approaches. Each strategy handles image processing differently, allowing you to optimize for your specific needs:

- **Runtime Strategy**: Immediate processing during page requests
- **Background Strategy**: Asynchronous processing via WordPress cron
- **WP CLI Strategy**: Batch processing via command line interface

## Why Multiple Strategies?

Different strategies address different performance and operational requirements:

| Strategy | Use Case | Performance Impact | Best For |
|----------|----------|-------------------|-----------|
| Runtime | Small sites, immediate results needed | Direct page load impact | Development, small traffic sites |
| Background | Production sites, user experience priority | Minimal page load impact | High traffic sites, production |
| WP CLI | Maintenance, bulk operations | No page load impact | Migrations, batch processing |

---

## Strategy Configuration

### Strategy Selection

Configure your preferred strategy by defining a constant in your `wp-config.php` or theme's `functions.php`:

```php
// Runtime Strategy (Default)
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'runtime');

// Background Strategy  
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'background');

// WP CLI Strategy
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'wpcli');
```

---

## Runtime Strategy

### Purpose
Processes images immediately during page requests, providing instant results but potentially impacting page load times.

### When to Use
- Development environments
- Low traffic websites (< 1000 visitors/day)
- When immediate image availability is critical
- Testing and debugging image conversion

### Configuration
```php
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'runtime');
```

### Performance Characteristics
- **Pros**: Immediate results, simple debugging, no queue management
- **Cons**: Can slow page loads, resource intensive during traffic spikes
- **Page Load Impact**: 100-500ms additional load time per image
- **Resource Usage**: High CPU during requests

### Example Usage
```php
// Images are converted immediately when requested
$image = wp_get_attachment_image($id, [800, 600]);
// Conversion happens synchronously during this call
```

### Monitoring
```php
// Monitor conversion times
add_action('Municipio/ImageConvert/Convert', function($data) {
    error_log("Runtime conversion: Image {$data['image_id']} - {$data['format']}");
});
```

---

## Background Strategy

### Purpose
Queues image conversions for background processing via WordPress cron, eliminating page load impact while ensuring conversions happen automatically.

### When to Use
- Production websites
- High traffic sites (> 1000 visitors/day)
- When user experience is prioritized
- Sites with regular content updates

### Configuration
```php
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'background');

// Optional: Configure cache expiration times
add_filter('Municipio/ImageConvert/Config/FailedCacheExpiry', function($seconds) {
    return 7200; // 2 hours instead of default 1 hour
});

add_filter('Municipio/ImageConvert/Config/SuccessCacheExpiry', function($seconds) {
    return 86400 * 7; // 1 week instead of default 24 hours
});
```

### Performance Characteristics
- **Pros**: Zero page load impact, automatic processing, efficient resource usage
- **Cons**: Delayed image availability, requires cron setup
- **Page Load Impact**: Near zero (returns original image immediately)
- **Resource Usage**: Distributed via cron jobs

### WordPress Cron Setup
Ensure WordPress cron is properly configured:

```bash
# Add to server crontab for reliable execution
*/5 * * * * curl -s https://yoursite.com/wp-cron.php >/dev/null 2>&1
```

Or disable WP cron and use system cron:
```php
// In wp-config.php
define('DISABLE_WP_CRON', true);
```

### Queue Processing
The background processor runs hourly and processes conversions via the action namespace:
- `Municipio/ImageConvert/Convert` - Triggers conversion requests
- `Municipio/ImageConvert/ProcessQueue` - Processes queued conversions

### Example Usage
```php
// Images are queued for conversion, original returned immediately
$image = wp_get_attachment_image($id, [800, 600]);
// User sees original image, conversion happens in background
```

### Monitoring
```php
// Monitor queue activity
add_action('Municipio/ImageConvert/Convert', function($data) {
    error_log("Queued for background: Image {$data['image_id']} - {$data['format']}");
});

// Monitor processing
add_action('Municipio/ImageConvert/ProcessQueue', function() {
    error_log("Background processing started");
});
```

---

## WP CLI Strategy

### Purpose
Optimized for batch processing and maintenance operations via command line, providing maximum control and efficiency for large-scale conversions.

### When to Use
- Site migrations
- Bulk image processing
- Maintenance operations
- When you need precise control over conversion timing
- Initial setup of converted image library

### Configuration
```php
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'wpcli');
```

### Performance Characteristics
- **Pros**: Maximum efficiency, detailed progress reporting, no user impact
- **Cons**: Requires command line access, manual execution
- **Page Load Impact**: Zero (conversions happen offline)
- **Resource Usage**: Configurable batch sizes, efficient memory usage

### WP CLI Commands

The strategy integrates with WP CLI for powerful batch operations:

```bash
# Basic usage - convert all images
wp image-convert process --strategy=wpcli

# Convert specific image sizes
wp image-convert process --size=medium --strategy=wpcli

# Convert with progress reporting
wp image-convert process --strategy=wpcli --progress

# Convert specific format
wp image-convert process --format=webp --strategy=wpcli

# Batch processing with limits
wp image-convert process --strategy=wpcli --batch-size=50 --delay=2

# Dry run to see what would be processed
wp image-convert process --strategy=wpcli --dry-run
```

### Example WP CLI Implementation
```php
/**
 * Custom WP CLI command for image conversion
 */
class ImageConvertCommand extends WP_CLI_Command {
    
    /**
     * Process image conversions using WP CLI strategy
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function process($args, $assoc_args) {
        $strategy = $assoc_args['strategy'] ?? 'wpcli';
        $batchSize = $assoc_args['batch-size'] ?? 10;
        $format = $assoc_args['format'] ?? 'webp';
        
        WP_CLI::line("Starting batch conversion with {$strategy} strategy...");
        
        // Process images in batches
        $processed = 0;
        $successful = 0;
        $failed = 0;
        
        // Implementation would go here
        
        WP_CLI::success("Processed: {$processed}, Successful: {$successful}, Failed: {$failed}");
    }
}

WP_CLI::add_command('image-convert', 'ImageConvertCommand');
```

### Monitoring
```php
// CLI-specific logging with detailed output
add_action('Municipio/ImageConvert/Convert', function($data) {
    if ($data['strategy'] === 'wpcli' && defined('WP_CLI') && WP_CLI) {
        WP_CLI::log("Processing: Image {$data['image_id']} -> {$data['format']}");
    }
});
```

---

## Advanced Configuration

### Cache Configuration
```php
// Adjust cache expiration times
add_filter('Municipio/ImageConvert/Config/FailedCacheExpiry', function($seconds) {
    return 3600; // 1 hour for failed conversions
});

add_filter('Municipio/ImageConvert/Config/SuccessCacheExpiry', function($seconds) {
    return 86400; // 24 hours for successful conversions
});

add_filter('Municipio/ImageConvert/Config/LockExpiry', function($seconds) {
    return 300; // 5 minutes for conversion locks
});
```

### Performance Tuning
```php
// Background processing batch size
add_filter('Municipio/ImageConvert/Config/BatchSize', function($size) {
    return 5; // Process 5 images per cron run
});

// WP CLI memory limits
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 0);
```

### Error Handling
```php
// Custom error logging
add_action('Municipio/ImageConvert/Error', function($error, $imageId, $format) {
    error_log("Image conversion error for {$imageId} ({$format}): {$error}");
}, 10, 3);
```

---

## Troubleshooting

### Common Issues

**Runtime Strategy**
- **Slow page loads**: Switch to background strategy
- **Memory errors**: Increase PHP memory limit or use CLI strategy
- **Timeout errors**: Reduce batch sizes or switch strategies

**Background Strategy**
- **Images not converting**: Check WordPress cron setup
- **Queue backing up**: Increase cron frequency or batch size
- **Conversions not running**: Verify action hooks are properly registered

**WP CLI Strategy**
- **Command not found**: Ensure WP CLI is properly installed
- **Memory errors**: Increase PHP memory limits for CLI
- **Permission errors**: Check file system permissions

### Debug Mode
```php
// Enable detailed logging
define('MUNICIPIO_IMAGE_CONVERT_DEBUG', true);

// This will log all conversion attempts and cache operations
```

### Performance Monitoring
```php
// Track conversion performance
add_action('Municipio/ImageConvert/Complete', function($duration, $imageId) {
    if ($duration > 5000) { // Log slow conversions (>5s)
        error_log("Slow conversion: Image {$imageId} took {$duration}ms");
    }
}, 10, 2);
```

---

## Migration Between Strategies

### From Runtime to Background
```php
// No code changes needed, just update the constant
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'background');

// Optionally clear failed conversion cache to retry with new strategy
wp_cache_flush();
```

### From Background to WP CLI
```php
// Update strategy
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'wpcli');

// Process any queued conversions manually
wp image-convert process --strategy=wpcli
```

### Bulk Re-conversion
```bash
# Clear conversion cache and reconvert all images
wp cache flush
wp image-convert process --strategy=wpcli --force
```

---

## Best Practices

1. **Development**: Use runtime strategy for immediate feedback
2. **Staging**: Test background strategy to verify cron setup
3. **Production**: Use background strategy for user experience
4. **Maintenance**: Use WP CLI strategy for bulk operations
5. **Monitor**: Implement logging to track conversion performance
6. **Cache**: Leverage the built-in caching to avoid redundant conversions
7. **Fallback**: Always ensure original images are served if conversion fails

## Support

For additional support or custom implementations, refer to the strategy interface documentation or create custom strategies by implementing `ConversionStrategyInterface`.