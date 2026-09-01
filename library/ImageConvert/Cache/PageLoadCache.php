<?php

namespace Municipio\ImageConvert\Cache;

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
    private static array $requestCache = [];

    public function __construct(
        private ImageConvertConfigInterface $config
    ) {}

    /**
     * Check if an image conversion has been processed in the current request
     *
     * @param ImageContract $image
     * @return bool
     */
    public function hasBeenProcessedInCurrentRequest(ImageContract $image): bool
    {
        return isset(self::$requestCache[$this->getRequestCacheKey($image)]);
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
    }

    /**
     * Clear page load cache for a specific image (e.g., when image is updated)
     *
     * @param int $imageId
     */
    public function clearImageCache(int $imageId): void
    {
        $pattern = "image_{$imageId}_";
        foreach (array_keys(self::$requestCache) as $key) {
            if (strpos($key, $pattern) === 0) {
                unset(self::$requestCache[$key]);
            }
        }
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
}
