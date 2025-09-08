<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ConversionCache;
use WpService\Contracts\DoAction;
use WpService\Contracts\WpScheduleSingleEvent;
use WpService\Contracts\WpUnscheduleEvent;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\WpNextScheduled;

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
        private DoAction&WpUnscheduleEvent&WpScheduleSingleEvent&WpNextScheduled $wpService,
        private ImageConvertConfig $config,
        private ConversionCache $conversionCache
    ) {
    }

    public function process(ImageContract $image): ImageContract|false
    {
        $imageId = $image->getId();
        $width   = $image->getWidth();
        $height  = $image->getHeight();
        $format  = $this->config->intermidiateImageFormat()['suffix'];

        $isAlreadyProcessing            = $this->conversionCache->isConversionLocked($imageId, $width, $height, $format);
        $isAlreadyQueuedForProcessing   = $this->conversionCache->isQueuedForConversion($imageId, $width, $height, $format);

        if ($isAlreadyProcessing || $isAlreadyQueuedForProcessing) {
            return $image;
        }

        // Queue for background processing
        $conversionData = [
            'image_id'              => $imageId,
            'width'                 => $width,
            'height'                => $height,
            'format'                => $format,
            'original_url'          => $image->getUrl(),
            'original_path'         => $image->getPath(),
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

        // Return original image immediately - resizing will happen in background
        return $image;
    }

    /**
     * Get the strategy name/identifier
     * 
     * @return string
     */
    public function getName(): string
    {
        return 'background';
    }

    /**
     * Schedule background processing via WordPress cron
     * Ensures processing is scheduled within a short timeframe
     * to handle queued conversions promptly.
     */
    private function scheduleBackgroundProcessing(): void
    {
        $nextScheduled = $this->wpService->wpNextScheduled('Municipio/ImageConvert/ProcessQueue');
        $timeNow = time();
    
        if (!$nextScheduled || ($nextScheduled - $timeNow) > 300) {
            if ($nextScheduled && ($nextScheduled - $timeNow) > 300) {
                $this->wpService->wpUnscheduleEvent($nextScheduled, 'Municipio/ImageConvert/ProcessQueue');
            }
            $this->wpService->wpScheduleSingleEvent($timeNow + 30, 'Municipio/ImageConvert/ProcessQueue');
        }
    }
}