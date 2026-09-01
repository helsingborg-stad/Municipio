<?php

namespace Municipio\ImageConvert;

use Municipio\Helper\File;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Cache\ConversionCache;
use Municipio\ImageConvert\Cache\PageLoadCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Logging\Log;
use Municipio\ImageConvert\Strategy\ConversionStrategyInterface;
use Municipio\ImageConvert\Strategy\StrategyFactory;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\DoAction;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\WpCacheDelete;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpDeleteFile;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\WpUploadDir;

class IntermidiateImageHandler implements Hookable
{
    private ConversionCache $conversionCache;
    private PageLoadCache $pageLoadCache;
    private ConversionStrategyInterface $conversionStrategy;

    public function __construct(
        private AddFilter&isWpError&WpGetImageEditor&WpUploadDir&WpGetAttachmentMetadata&IsAdmin&WpAttachmentIs&WpCacheGet&WpCacheSet&WpCacheDelete&DoAction&ApplyFilters&WpDeleteFile $wpService,
        private ImageConvertConfig $config,
        private Log $log,
    ) {
        $this->conversionCache = new ConversionCache($wpService, $config);
        $this->pageLoadCache = new PageLoadCache($wpService, $config);

        $strategyFactory = new StrategyFactory(
            $wpService,
            $config,
            $this->conversionCache,
            $this->log,
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
            1,
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
                ['image' => $image, 'format' => $format, 'reason' => 'recent_failure'],
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
                ['image' => $image, 'format' => $format, 'reason' => 'no_intermediate_location'],
            );

            return $image;
        }

        // Only reuse complete images with the expected type and dimensions.
        if ($this->isValidIntermediateImage($image, $intermediateLocation['path'])) {
            $image->setUrl($intermediateLocation['url']);
            $image->setPath($intermediateLocation['path']);

            // Mark as successful for future reference
            $this->conversionCache->markConversionSuccess($image);

            // Mark as processed in current request
            $this->pageLoadCache->markProcessedInCurrentRequest($image);

            return $image;
        }

        // A failed editor can leave an empty or malformed file behind. Remove it
        // before retrying so it can never be mistaken for a successful result.
        if (file_exists($intermediateLocation['path'])) {
            $this->wpService->wpDeleteFile($intermediateLocation['path']);

            if (file_exists($intermediateLocation['path'])) {
                $this->log->log(
                    $this,
                    'Could not remove an invalid intermediate image, skipping conversion.',
                    'warning',
                    ['image' => $image, 'format' => $format, 'reason' => 'invalid_intermediate_cleanup_failed'],
                );
                $this->conversionCache->markConversionFailed($image);

                return $image;
            }
        }

        // A failed conversion in this request must fall back to the source image
        // instead of exposing the missing or invalid intermediate URL.
        if ($this->pageLoadCache->hasBeenProcessedInCurrentRequest($image)) {
            return $image;
        }

        // Mark as processed in current request to prevent duplicate processing
        $this->pageLoadCache->markProcessedInCurrentRequest($image);

        // Use the selected conversion strategy
        return $this->conversionStrategy->process($image);
    }

    private function isValidIntermediateImage(ImageContract $image, string $path): bool
    {
        $sourceSize = File::getImageSizeWithoutWarnings($image->getPath());
        if ($sourceSize === false) {
            return false;
        }

        return File::isValidImage(
            $path,
            File::getImageMimeTypeForPath($path),
            min($image->getWidth(), $sourceSize[0]),
            min($image->getHeight(), $sourceSize[1]),
        );
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
