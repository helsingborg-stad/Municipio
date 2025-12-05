<?php

namespace Municipio\ImageConvert\Cache;

use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpCacheDelete;
use Municipio\ImageConvert\Config\ImageConvertConfigInterface;
use Municipio\ImageConvert\Contract\ImageContract;

/**
 * PageLoadCache
 *
 * Manages page load caching to prevent multiple image generations
 * within the same page load or request lifecycle.
 */
class PageLoadCache
{
    private const CACHE_GROUP      = 'municipio_page_load';
    private const PAGE_LOAD_PREFIX = 'page_load_';
    private const REQUEST_PREFIX   = 'request_';

    private static array $requestCache       = [];
    private static ?string $currentRequestId = null;

    public function __construct(
        private WpCacheGet&WpCacheSet&WpCacheDelete $wpService,
        private ImageConvertConfigInterface $config
    ) {
        if (self::$currentRequestId === null) {
            self::$currentRequestId = $this->generateRequestId();
        }
    }

    /**
     * Check if an image conversion has been processed in the current request
     *
     * @param ImageContract $image
     * @return bool
     */
    public function hasBeenProcessedInCurrentRequest(ImageContract $image): bool
    {
        $requestKey = $this->getRequestCacheKey($image);

        if (isset(self::$requestCache[$requestKey])) {
            return true;
        }

        return (bool) $this->wpService->wpCacheGet(
            self::REQUEST_PREFIX . self::$currentRequestId . '_' . $requestKey,
            self::CACHE_GROUP
        );
    }

    /**
     * Mark that an image conversion has been processed in the current request
     *
     * @param ImageContract $image
     */
    public function markProcessedInCurrentRequest(ImageContract $image): void
    {
        $requestKey = $this->getRequestCacheKey($image);

        self::$requestCache[$requestKey] = true;
        $this->wpService->wpCacheSet(
            self::REQUEST_PREFIX . self::$currentRequestId . '_' . $requestKey,
            true,
            self::CACHE_GROUP,
            $this->config->pageCacheExpiry()
        );
    }

    /**
     * Clear page load cache for a specific image (e.g., when image is updated)
     *
     * @param int $imageId
     */
    public function clearImageCache(int $imageId): void
    {
        $pageLoadKey = $this->getPageLoadCacheKey($imageId);
        $this->wpService->wpCacheDelete($pageLoadKey, self::CACHE_GROUP);

        // Clear from runtime cache as well
        $pattern = "image_{$imageId}_";
        foreach (array_keys(self::$requestCache) as $key) {
            if (strpos($key, $pattern) === 0) {
                unset(self::$requestCache[$key]);
            }
        }
    }

    /**
     * Get the cache key for page load tracking
     */
    private function getPageLoadCacheKey(int $imageId): string
    {
        return self::PAGE_LOAD_PREFIX . $imageId;
    }

    /**
     * Get the cache key for request-level tracking
     *
     * @param ImageContract $image
     * @return string
     */
    private function getRequestCacheKey(ImageContract $image): string
    {
        $imageId = $image->getId();
        $width   = $image->getWidth();
        $height  = $image->getHeight();
        $format  = $this->config->intermidiateImageFormat()['suffix'];
        return sprintf('image_%d_%dx%d_%s', $imageId, $width, $height, $format);
    }

    /**
     * Generate a unique request ID for the current request
     */
    private function generateRequestId(): string
    {
        // Use a combination of factors to create a unique request ID
        $factors = [
            $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true),
            $_SERVER['REQUEST_URI'] ?? 'cli',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            getmypid(), // Process ID
            uniqid('', true)
        ];

        return hash('crc32b', implode('|', $factors));
    }
}
