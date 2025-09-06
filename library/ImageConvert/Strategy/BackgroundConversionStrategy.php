<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ConversionCache;
use WpService\Contracts\DoAction;

/**
 * Background Conversion Strategy
 * 
 * Queues image conversion for background processing via WordPress cron.
 * Returns the original image immediately to avoid blocking page loads.
 * Conforms to action namespace Municipio/ImageConvert/Convert.
 */
class BackgroundConversionStrategy implements ConversionStrategyInterface
{
    public function __construct(
        private DoAction $wpService,
        private ConversionCache $conversionCache
    ) {
    }

    public function convert(ImageContract $image, string $format): ImageContract|false
    {
        $imageId = $image->getId();
        $width = $image->getWidth();
        $height = $image->getHeight();

        // Check if conversion is already in progress or queued
        if ($this->conversionCache->isConversionLocked($imageId, $width, $height, $format) ||
            $this->conversionCache->isQueuedForConversion($imageId, $width, $height, $format)) {
            // Already being processed or queued, return original image
            return $image;
        }

        // Queue for background processing
        $conversionData = [
            'image_id' => $imageId,
            'width' => $width,
            'height' => $height,
            'format' => $format,
            'original_url' => $image->getUrl(),
            'original_path' => $image->getPath(),
            'intermediate_location' => $image->getIntermidiateLocation($format)
        ];

        $this->conversionCache->queueForBackgroundConversion($imageId, $width, $height, $format, $conversionData);

        // Trigger the conversion action using the specified namespace
        $this->wpService->doAction(
            'Municipio/ImageConvert/Convert',
            $conversionData
        );

        // Schedule background processing if not already scheduled
        $this->scheduleBackgroundProcessing();

        // Return original image immediately - conversion will happen in background
        return $image;
    }

    public function canHandle(ImageContract $image, string $format): bool
    {
        // Background strategy can handle any image conversion
        return true;
    }

    public function getName(): string
    {
        return 'background';
    }

    /**
     * Schedule background processing via WordPress cron with improved frequency
     */
    private function scheduleBackgroundProcessing(): void
    {
        // Check if a single event is already scheduled soon
        $nextScheduled = wp_next_scheduled('Municipio/ImageConvert/ProcessQueue');
        $timeNow = time();
        
        // Only schedule if not already scheduled or if the next scheduled time is more than 5 minutes away
        if (!$nextScheduled || ($nextScheduled - $timeNow) > 300) {
            // Clear any existing single event that's too far in the future
            if ($nextScheduled && ($nextScheduled - $timeNow) > 300) {
                wp_unschedule_event($nextScheduled, 'Municipio/ImageConvert/ProcessQueue');
            }
            
            // Schedule for processing in the next 30 seconds for faster response
            wp_schedule_single_event($timeNow + 30, 'Municipio/ImageConvert/ProcessQueue');
        }
    }
}