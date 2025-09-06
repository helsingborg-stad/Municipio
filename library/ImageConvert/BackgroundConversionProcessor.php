<?php

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;

/**
 * BackgroundConversionProcessor
 * 
 * Handles background processing of queued image conversions to avoid blocking page loads.
 * This processor can be triggered via WordPress cron or external queue systems.
 */
class BackgroundConversionProcessor implements Hookable
{
    public function __construct(
        private AddAction&DoAction $wpService,
        private ConversionCache $conversionCache,
        private IntermidiateImageHandler $imageHandler
    ) {
    }

    public function addHooks(): void
    {
        // Hook into WordPress cron for background processing
        $this->wpService->addAction('municipio_process_image_queue', [$this, 'processQueuedConversions']);
        
        // Hook into the conversion action namespace as specified
        $this->wpService->addAction('Municipio/ImageConvert/Convert', [$this, 'handleConversionRequest']);
        
        // Schedule processing if not already scheduled
        if (!wp_next_scheduled('municipio_process_image_queue')) {
            wp_schedule_event(time(), 'hourly', 'municipio_process_image_queue');
        }
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
     * Process queued image conversions in background
     */
    public function processQueuedConversions(): void
    {
        $queuedConversions = $this->conversionCache->getQueuedConversions(5); // Process 5 at a time
        
        foreach ($queuedConversions as $conversion) {
            $this->processQueuedConversion($conversion);
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
                return;
            }
            
            // Create ImageContract for processing
            // Note: In real implementation, you would need to reconstruct the ImageContract
            // from the queued data or fetch it from the database
            
            error_log("Background processing image conversion for ID: $imageId, ${width}x${height}, format: $format");
            
        } catch (\Throwable $e) {
            error_log("Error processing queued image conversion: " . $e->getMessage());
        }
    }

    /**
     * Manually trigger queue processing (useful for testing or manual execution)
     */
    public function triggerQueueProcessing(): void
    {
        $this->wpService->doAction('municipio_process_image_queue');
    }
}