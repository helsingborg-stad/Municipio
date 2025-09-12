<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Cache\ConversionCache;
use Municipio\ImageConvert\Cache\PageLoadCache;
use Municipio\ImageConvert\Strategy\StrategyFactory;
use Municipio\ImageConvert\Strategy\ConversionStrategyInterface;
use Municipio\ImageConvert\Logging\Log;
use WpService\Contracts\AddFilter;
use WpService\Contracts\IsWpError;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\DoAction;
use WpService\Contracts\ApplyFilters;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\Helper\File;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\WpUploadDir;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpCacheDelete;
use WP_Error;

class IntermidiateImageHandler implements Hookable
{
    private ConversionCache $conversionCache;
    private PageLoadCache $pageLoadCache;
    private ConversionStrategyInterface $conversionStrategy;

    public function __construct(
        private AddFilter&isWpError&WpGetImageEditor&WpUploadDir&WpGetAttachmentMetadata&IsAdmin&WpAttachmentIs&WpCacheGet&WpCacheSet&WpCacheDelete&DoAction&ApplyFilters $wpService,
        private ImageConvertConfig $config,
        private Log $log
    ) {
        $this->conversionCache = new ConversionCache($wpService, $config);
        $this->pageLoadCache   = new PageLoadCache($wpService, $config);

        $strategyFactory          = new StrategyFactory(
            $wpService,
            $config,
            $this->conversionCache,
            $this->log
        );
        $this->conversionStrategy = $strategyFactory->createStrategy();
    }

    /**
     * Register hooks
     */
    public function addHooks(): void
    {
        if ($this->wpService->isAdmin()) {
            return;
        }

        $this->wpService->addFilter(
            $this->config->createFilterKey('imageDownsize'),
            [$this, 'createIntermidiateImage'],
            $this->config->internalFilterPriority()->intermidiateImageConvert,
            1
        );

        // Clear conversion cache when attachments are deleted or updated
        $this->wpService->addFilter('delete_attachment', [$this, 'clearAttachmentCache'], 10, 1);
    }

    /**
     * Create intermediate image and set new URL and path
     *
     * @param ImageContract $image
     * @return ImageContract|bool
     */
    public function createIntermidiateImage($image): ImageContract|bool
    {
        if (!$image instanceof ImageContract) {
            return $image;
        }

        // Collect data
        $format = $this->config->intermidiateImageFormat()['suffix'];

        // If conversion has recently failed, return original image
        if ($this->conversionCache->hasRecentFailure($image)) {
            $this->log->log(
                $this,
                'Recent conversion failure detected, skipping conversion.',
                'warning',
                ['image' => $image, 'format' => $format, 'reason' => 'recent_failure']
            );

            return $image;
        }

        // Fallback if no intermediate location could be determined
        $intermediateLocation = $image->getIntermidiateLocation($format);
        if (empty($intermediateLocation['path']) || empty($intermediateLocation['url'])) {
            $this->log->log(
                $this,
                'Could not determine intermediate image location, skipping conversion.',
                'warning',
                ['image' => $image, 'format' => $format, 'reason' => 'no_intermediate_location']
            );

            return $image;
        }

        //If already processed in this request, return the intermediate image, it will exist anyway
        if ($this->pageLoadCache->hasBeenProcessedInCurrentRequest($image)) {
            $image->setUrl($intermediateLocation['url']);
            $image->setPath($intermediateLocation['path']);
            return $image;
        }

        // Check if the intermediate image already exists, if so return it
        if (File::fileExists($intermediateLocation['path'])) {
            $image->setUrl($intermediateLocation['url']);
            $image->setPath($intermediateLocation['path']);

            // Mark as successful for future reference
            $this->conversionCache->markConversionSuccess($image);

            // Mark as processed in current request
            $this->pageLoadCache->markProcessedInCurrentRequest($image);

            return $image;
        }

        // Mark as processed in current request to prevent duplicate processing
        $this->pageLoadCache->markProcessedInCurrentRequest($image);

        // Use the selected conversion strategy
        return $this->conversionStrategy->process($image);
    }

    /**
     * Clear conversion cache when an attachment is deleted
     *
     * @param int $attachmentId
     */
    public function clearAttachmentCache(int $attachmentId): void
    {
        $this->conversionCache->clearImageCache($attachmentId);
        $this->pageLoadCache->clearImageCache($attachmentId);
    }
}
