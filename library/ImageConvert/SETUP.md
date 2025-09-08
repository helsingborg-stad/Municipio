# Image Resize Strategy Setup

Municipio's ImageConvert system creates images on-demand by pixel size rather than using pre-generated named sizes. This documentation covers the five processing strategies available.

## Core Functionality

Use `wp_get_attachment_img_src($id, [100,100])` to create a 100x100 pixel version of any image. The system handles resizing automatically based on the selected strategy.

## Strategy Configuration

Set your strategy in `wp-config.php`:

```php
// Runtime (default) - immediate processing during page load
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'runtime');

// Background - queue for cron processing  
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'background');

// Mixed - runtime for editors, background for visitors
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'mixed');

// Async - immediate parallel processing using child processes
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'async');

// CLI - for batch operations
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'wpcli');
```

## Strategies

### Runtime (Default)
- **When**: Image resized during page load
- **Best for**: Low traffic sites, development environments
- **Performance**: Direct impact on page load time

### Background  
- **When**: Images queued for cron processing (every 5 minutes)
- **Best for**: High traffic production sites
- **Performance**: No page load impact, delayed image availability

### Mixed
- **When**: Runtime for editors who modified content within last hour, background for others
- **Best for**: Editorial workflows where editors need immediate feedback
- **Performance**: Smart balancing of editor experience vs visitor performance

### Async
- **When**: Image resized immediately using parallel child processes
- **Best for**: Sites needing immediate results without blocking requests
- **Performance**: Parallel processing eliminates blocking, moderate resource usage

### CLI
- **When**: Manual batch processing via WP CLI commands
- **Best for**: Migrations, bulk operations, maintenance
- **Performance**: No page load impact, controlled processing

## Performance Features

### Failure Suspension
Failed image conversions are suspended for 24 hours to prevent repeated processing failures.

### Caching Requirements
The system uses WordPress object cache. Redis or Memcached is strongly recommended for production environments.

### Configuration Filters

```php
// Adjust batch size for background processing
add_filter('Municipio/ImageConvert/Config/BatchSize', function($size) {
    return 10; // Process 10 images per 5-minute interval
});

// Modify failure suspension time  
add_filter('Municipio/ImageConvert/Config/FailedCacheExpiry', function($expiry) {
    return 86400; // 24 hours (default)
});
```

## Requirements

- WordPress object caching (Redis/Memcached recommended)
- Sufficient memory and processing time for image operations
- WordPress cron enabled (for background/mixed strategies)

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
The background processor runs every 5 minutes and processes conversions via the action namespace:
- `Municipio/ImageConvert/Convert` - Triggers conversion requests
- `Municipio/ImageConvert/ProcessQueue` - Processes queued conversions

**Improved Cron Scheduling:**
- Processes queue every 5 minutes instead of hourly for faster results
- Single conversion events scheduled 30 seconds in the future for immediate needs
- Parallel execution protection prevents multiple cron jobs from running simultaneously
- Automatic cleanup of expired or redundant scheduled events

### Example Usage
```php
// Images are queued for conversion, original returned immediately
$image = wp_get_attachment_image($id, [800, 600]);
// User sees original image, conversion happens in background within 5 minutes
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

## Async Strategy

### Purpose
Performs image conversion immediately using parallel child processes, providing instant results without blocking the main request thread.

### When to Use
- Sites requiring immediate image availability without page load blocking
- Applications with concurrent image processing needs
- When you need real-time results but want to avoid request blocking
- Development/testing environments requiring immediate feedback

### Configuration
```php
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'async');
```

### Performance Characteristics
- **Pros**: Immediate results, non-blocking processing, parallel execution
- **Cons**: Higher memory usage, requires child process support
- **Page Load Impact**: Minimal (processing happens in parallel)
- **Resource Usage**: Moderate CPU and memory per conversion

### Requirements
- PHP with pcntl extension support (for process control)
- Sufficient system resources for child processes
- spatie/async package (automatically included)

### How It Works
The async strategy uses the spatie/async library to:
1. Create child processes for image conversion
2. Process images in parallel to the main request
3. Return results immediately when processing completes
4. Handle errors gracefully with fallback options

### Example Usage
```php
// Image converts immediately in parallel process
$image = wp_get_attachment_image($id, [800, 600]);
// User receives converted image without request blocking
```

### Monitoring
```php
// Monitor async processing
add_action('Municipio/ImageConvert/Convert', function($data) {
    error_log("Async conversion: Image {$data['image_id']} - {$data['format']}");
});

// Monitor processing errors
add_action('Municipio/ImageConvert/Error', function($error, $imageId) {
    error_log("Async conversion failed for image {$imageId}: {$error}");
}, 10, 2);
```

### Troubleshooting
- **Process spawn errors**: Check pcntl extension availability
- **Memory issues**: Monitor child process memory usage
- **Permission errors**: Verify process creation permissions

---

## Mixed Strategy

### Purpose
Intelligently combines runtime and background processing based on editor context. Provides immediate results for editors working on content while maintaining background processing for regular users.

### When to Use
- Editorial teams and content creation workflows
- Sites where editors need immediate feedback
- CMS environments with active content management
- Balancing user experience with editor productivity

### Configuration
```php
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'mixed');

// Optional: Configure editor detection timeframe
add_filter('Municipio/ImageConvert/Config/EditorRecentActivityWindow', function($seconds) {
    return 7200; // 2 hours instead of default 1 hour
});
```

### How It Works

**Immediate Processing (Runtime) When:**
- Current user has `edit_posts` capability
- Current user modified the image within the last hour
- Current user modified the parent post within the last hour

**Background Processing When:**
- User is not logged in
- User doesn't have editor capabilities
- No recent activity by current user on the content

### Performance Characteristics
- **Pros**: Best of both worlds - editor efficiency + user experience
- **Cons**: More complex logic, requires user context
- **Page Load Impact**: Variable (immediate for editors, zero for users)
- **Resource Usage**: Optimized based on user role and activity

### Editorial Workflow Benefits
```php
// Editor uploads image and sees immediate conversion
// 1. Editor uploads new image to post
// 2. Views post preview immediately 
// 3. Image converts in real-time during preview
// 4. Editor sees final result without waiting

// Regular visitor experiences no delay
// 1. Visitor views same post
// 2. Original image served immediately
// 3. Conversion happens in background
// 4. Converted image available on next visit
```

### Example Usage
```php
// Behavior varies based on user context
$image = wp_get_attachment_image($id, [800, 600]);

// If current user is editor who recently modified content:
//   -> Processes immediately (runtime behavior)
// If regular user or no recent editor activity:
//   -> Queues for background processing
```

### Monitoring
```php
// Monitor strategy decisions
add_action('Municipio/ImageConvert/StrategySelected', function($strategy, $imageId, $userId) {
    error_log("Mixed strategy selected {$strategy} for image {$imageId} by user {$userId}");
}, 10, 3);

// Monitor editor activity detection
add_action('Municipio/ImageConvert/EditorActivityDetected', function($userId, $imageId) {
    error_log("Editor activity detected: User {$userId} for image {$imageId}");
}, 10, 2);
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
- **Images not converting**: Check WordPress cron setup and ensure 5-minute intervals are working
- **Queue backing up**: Increase batch size or check for parallel execution issues
- **Conversions not running**: Verify action hooks are properly registered
- **Multiple cron jobs**: Check logs for parallel execution warnings - system prevents this automatically

**Mixed Strategy**
- **Editor not getting immediate processing**: Check user capabilities and recent activity detection
- **Background processing not working**: Verify background strategy components are functioning
- **Inconsistent behavior**: Monitor strategy selection logs to understand decision logic

**Async Strategy**
- **Process spawn errors**: Check pcntl extension availability and system support for child processes
- **Memory issues**: Monitor child process memory usage and adjust limits if needed
- **Permission errors**: Verify process creation permissions and file system access
- **Slow processing**: Check system load and concurrent process limits

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

### From Background to Mixed
```php
// Update strategy for editorial workflow optimization
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'mixed');

// Test editor detection by logging in as editor and modifying content
```

### From Runtime to Mixed
```php
// Transition to intelligent processing
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'mixed');

// Editors continue to get immediate results, regular users get background processing
```

### From Runtime to Async
```php
// Switch to parallel processing for non-blocking immediate results
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'async');

// Test by checking that page loads aren't blocked during image conversion
```

### From Background to Async
```php
// Switch from cron-based to immediate parallel processing
define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'async');

// Clear any pending background queue
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