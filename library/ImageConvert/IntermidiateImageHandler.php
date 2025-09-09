<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ConversionCache;
use Municipio\ImageConvert\PageLoadCache;
use Municipio\ImageConvert\Strategy\StrategyFactory;
use Municipio\ImageConvert\Strategy\ConversionStrategyInterface;
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

    public function __construct(private AddFilter&isWpError&WpGetImageEditor&WpUploadDir&WpGetAttachmentMetadata&IsAdmin&WpAttachmentIs&WpCacheGet&WpCacheSet&WpCacheDelete&DoAction&ApplyFilters $wpService, private ImageConvertConfig $config)
    {
        $this->conversionCache = new ConversionCache($wpService);
        $this->pageLoadCache = new PageLoadCache($wpService);
        
        // Create conversion strategy based on configuration
        $strategyFactory = new StrategyFactory($wpService, $config, $this->conversionCache);
        $this->conversionStrategy = $strategyFactory->createStrategy();
    }

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
        $this->wpService->addFilter('wp_update_attachment_metadata', [$this, 'clearAttachmentCacheOnUpdate'], 10, 2);
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
            return $image; // Fallback to original if not an instance of ImageContract
        }

        // Collect data
        $format = $this->config->intermidiateImageFormat()['suffix'];
        $imageId = $image->getId();
        $width = $image->getWidth();
        $height = $image->getHeight();

        if ($this->conversionCache->hasRecentFailure($imageId, $width, $height, $format)) {
            return $image;
        }

        // Fallback if no intermediate location could be determined
        $intermediateLocation = $image->getIntermidiateLocation($format);
        if (empty($intermediateLocation['path']) || empty($intermediateLocation['url'])) {
            return $image;
        }

        //If already processed in this request, return the intermediate image, it will exist anyway
        if ($this->pageLoadCache->hasBeenProcessedInCurrentRequest($imageId, $width, $height, $format)) {
            $image->setUrl($intermediateLocation['url']);
            $image->setPath($intermediateLocation['path']);
            return $image; 
        }

        // Check if the intermediate image already exists, if so return it
        if (File::fileExists($intermediateLocation['path'])) {
            $image->setUrl($intermediateLocation['url']);
            $image->setPath($intermediateLocation['path']);
            
            // Mark as successful for future reference
            $this->conversionCache->markConversionSuccess($imageId, $width, $height, $format);
            
            // Mark as processed in current request
            $this->pageLoadCache->markProcessedInCurrentRequest($imageId, $width, $height, $format);
            
            return $image;
        }

        // Mark as processed in current request to prevent duplicate processing
        $this->pageLoadCache->markProcessedInCurrentRequest($imageId, $width, $height, $format);

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

    /**
     * Clear conversion cache when attachment metadata is updated
     *
     * @param array $data
     * @param int $attachmentId
     * @return array
     */
    public function clearAttachmentCacheOnUpdate(array $data, int $attachmentId): array
    {
        $this->clearAttachmentCache($attachmentId);
        return $data;
    }
}
