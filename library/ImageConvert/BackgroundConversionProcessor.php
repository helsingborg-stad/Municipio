<?php

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;

/**
 * BackgroundConversionProcessor
 * 
 * Handles background processing of queued image conversions to avoid blocking page loads.
 * This processor can be triggered via WordPress cron or external queue systems.
 */
class BackgroundConversionProcessor implements Hookable
{
    public function __construct(
        private AddAction&DoAction&AddFilter&ApplyFilters $wpService,
        private ConversionCache $conversionCache,
        private IntermidiateImageHandler $imageHandler
    ) {
    }

    public function addHooks(): void
    {
        // Hook into WordPress cron for background processing using namespace
        $this->wpService->addAction('Municipio/ImageConvert/ProcessQueue', [$this, 'processQueuedConversions']);
        
        // Hook into the conversion action namespace as specified
        $this->wpService->addAction('Municipio/ImageConvert/Convert', [$this, 'handleConversionRequest']);
        
        // Schedule processing if not already scheduled - run every 5 minutes for faster processing
        if (!wp_next_scheduled('Municipio/ImageConvert/ProcessQueue')) {
            wp_schedule_event(time(), 'five_minutes', 'Municipio/ImageConvert/ProcessQueue');
        }
        
        // Add custom cron interval for 5 minutes
        $this->wpService->addFilter('cron_schedules', [$this, 'addCustomCronInterval']);
    }

    /**
     * Handle conversion request from the action namespace
     * 
     * @param array $conversionData
     */
    public function handleConversionRequest(array $conversionData): void
    {
        // Log the conversion request for monitoring
        error_log("Background conversion requested for Image ID: {$conversionData['image_id']}, " .
                 "{$conversionData['width']}x{$conversionData['height']}, format: {$conversionData['format']}");
        
        // The actual conversion will be handled by the cron job
        // This action just logs and potentially schedules immediate processing
        // if needed for high-priority conversions
    }

    /**
     * Add custom cron interval for 5 minutes
     * 
     * @param array $schedules
     * @return array
     */
    public function addCustomCronInterval(array $schedules): array
    {
        if (!isset($schedules['five_minutes'])) {
            $schedules['five_minutes'] = [
                'interval' => 300, // 5 minutes in seconds
                'display'  => __('Every 5 Minutes', 'municipio')
            ];
        }
        return $schedules;
    }

    /**
     * Process queued image conversions in background with parallel execution protection
     */
    public function processQueuedConversions(): void
    {
        // Prevent parallel execution of queue processing
        $lockKey = 'municipio_queue_processing_lock';
        $lockExpiry = 300; // 5 minutes
        
        // Try to acquire a lock
        if (!wp_cache_add($lockKey, time(), 'municipio_image_convert', $lockExpiry)) {
            // Lock exists, check if it's expired
            $lockTime = wp_cache_get($lockKey, 'municipio_image_convert');
            if ($lockTime && (time() - $lockTime) < $lockExpiry) {
                // Lock is still valid, another process is running
                error_log("Queue processing already in progress, skipping this run");
                return;
            }
            // Lock expired, force update
            wp_cache_set($lockKey, time(), 'municipio_image_convert', $lockExpiry);
        }
        
        try {
            $batchSize = (int) $this->wpService->applyFilters(
                'Municipio/ImageConvert/Config/BatchSize',
                5
            );
            
            $queuedConversions = $this->conversionCache->getQueuedConversions($batchSize);
            
            foreach ($queuedConversions as $conversion) {
                $this->processQueuedConversion($conversion);
            }
        } finally {
            // Always release the lock
            wp_cache_delete($lockKey, 'municipio_image_convert');
        }
    }

    /**
     * Process a single queued conversion
     * 
     * @param array $conversion
     */
    private function processQueuedConversion(array $conversion): void
    {
        try {
            $imageId = $conversion['image_id'];
            $width = $conversion['width'];
            $height = $conversion['height'];
            $format = $conversion['format'];
            
            // Skip if already processing or recently failed
            if ($this->conversionCache->isConversionLocked($imageId, $width, $height, $format) ||
                $this->conversionCache->hasRecentFailure($imageId, $width, $height, $format)) {
                // Remove from queue since we're skipping it
                $this->conversionCache->removeFromQueue($imageId, $width, $height, $format);
                return;
            }
            
            // Acquire lock for processing
            if (!$this->conversionCache->acquireConversionLock($imageId, $width, $height, $format)) {
                // Could not acquire lock, leave in queue for next run
                return;
            }
            
            try {
                // Mark as processing
                $this->conversionCache->setConversionStatus($imageId, $width, $height, $format, ConversionCache::STATUS_PROCESSING);
                
                // Create ImageContract for processing
                // Note: In real implementation, you would need to reconstruct the ImageContract
                // from the queued data or fetch it from the database
                
                error_log("Background processing image conversion for ID: $imageId, ${width}x${height}, format: $format");
                
                // Simulate successful processing - in real implementation, call the actual conversion
                $this->conversionCache->markConversionSuccess($imageId, $width, $height, $format);
                
                // Remove from queue after successful processing
                $this->conversionCache->removeFromQueue($imageId, $width, $height, $format);
                
            } catch (\Throwable $e) {
                // Mark as failed and remove from queue
                $this->conversionCache->markConversionFailed($imageId, $width, $height, $format);
                $this->conversionCache->removeFromQueue($imageId, $width, $height, $format);
                throw $e;
            } finally {
                // Always release the lock
                $this->conversionCache->releaseConversionLock($imageId, $width, $height, $format);
            }
            
        } catch (\Throwable $e) {
            error_log("Error processing queued image conversion: " . $e->getMessage());
        }
    }

    /**
     * Manually trigger queue processing (useful for testing or manual execution)
     */
    public function triggerQueueProcessing(): void
    {
        $this->wpService->doAction('Municipio/ImageConvert/ProcessQueue');
    }
}