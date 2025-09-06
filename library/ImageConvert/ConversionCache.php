<?php

namespace Municipio\ImageConvert;

use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpCacheDelete;

/**
 * ConversionCache
 * 
 * Manages caching and deduplication for image conversion operations.
 * Provides mechanisms to prevent duplicate processing and cache conversion results.
 */
class ConversionCache
{
    private const CACHE_GROUP = 'municipio_image_convert';
    private const STATUS_PREFIX = 'status_';
    private const LOCK_PREFIX = 'lock_';
    private const QUEUE_PREFIX = 'queue_';
    
    // Cache expiration times
    private const FAILED_CACHE_EXPIRY = 3600; // 1 hour for failed conversions
    private const SUCCESS_CACHE_EXPIRY = 86400; // 24 hours for successful conversions
    private const LOCK_EXPIRY = 300; // 5 minutes for conversion locks
    
    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    
    private static array $runtimeCache = [];

    public function __construct(
        private WpCacheGet&WpCacheSet&WpCacheDelete $wpService
    ) {
    }

    /**
     * Get conversion cache key for an image conversion request
     */
    private function getCacheKey(int $imageId, int $width, int $height, string $format): string
    {
        return sprintf('%d_%dx%d_%s', $imageId, $width, $height, $format);
    }

    /**
     * Check if a conversion is currently in progress (locked)
     */
    public function isConversionLocked(int $imageId, int $width, int $height, string $format): bool
    {
        $cacheKey = self::LOCK_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        // Check runtime cache first
        if (isset(self::$runtimeCache[$cacheKey])) {
            return self::$runtimeCache[$cacheKey];
        }
        
        $isLocked = (bool) $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
        self::$runtimeCache[$cacheKey] = $isLocked;
        
        return $isLocked;
    }

    /**
     * Acquire a lock for image conversion to prevent duplicate processing
     */
    public function acquireConversionLock(int $imageId, int $width, int $height, string $format): bool
    {
        $cacheKey = self::LOCK_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        // Try to acquire lock
        $acquired = $this->wpService->wpCacheSet($cacheKey, time(), self::CACHE_GROUP, self::LOCK_EXPIRY);
        
        if ($acquired) {
            self::$runtimeCache[$cacheKey] = true;
        }
        
        return $acquired;
    }

    /**
     * Release a conversion lock
     */
    public function releaseConversionLock(int $imageId, int $width, int $height, string $format): bool
    {
        $cacheKey = self::LOCK_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        unset(self::$runtimeCache[$cacheKey]);
        
        return $this->wpService->wpCacheDelete($cacheKey, self::CACHE_GROUP);
    }

    /**
     * Get the conversion status for an image
     */
    public function getConversionStatus(int $imageId, int $width, int $height, string $format): ?string
    {
        $cacheKey = self::STATUS_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        // Check runtime cache first
        if (isset(self::$runtimeCache[$cacheKey])) {
            return self::$runtimeCache[$cacheKey];
        }
        
        $status = $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
        
        if ($status) {
            self::$runtimeCache[$cacheKey] = $status;
        }
        
        return $status ?: null;
    }

    /**
     * Set the conversion status for an image
     */
    public function setConversionStatus(int $imageId, int $width, int $height, string $format, string $status): bool
    {
        $cacheKey = self::STATUS_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        // Determine cache expiry based on status
        $expiry = match ($status) {
            self::STATUS_FAILED => self::FAILED_CACHE_EXPIRY,
            self::STATUS_SUCCESS => self::SUCCESS_CACHE_EXPIRY,
            default => 300 // 5 minutes for pending/processing
        };
        
        self::$runtimeCache[$cacheKey] = $status;
        
        return $this->wpService->wpCacheSet($cacheKey, $status, self::CACHE_GROUP, $expiry);
    }

    /**
     * Check if a conversion recently failed and should be skipped
     */
    public function hasRecentFailure(int $imageId, int $width, int $height, string $format): bool
    {
        $status = $this->getConversionStatus($imageId, $width, $height, $format);
        return $status === self::STATUS_FAILED;
    }

    /**
     * Mark a conversion as successful
     */
    public function markConversionSuccess(int $imageId, int $width, int $height, string $format): bool
    {
        return $this->setConversionStatus($imageId, $width, $height, $format, self::STATUS_SUCCESS);
    }

    /**
     * Mark a conversion as failed
     */
    public function markConversionFailed(int $imageId, int $width, int $height, string $format): bool
    {
        return $this->setConversionStatus($imageId, $width, $height, $format, self::STATUS_FAILED);
    }

    /**
     * Queue an image for background conversion
     */
    public function queueForBackgroundConversion(int $imageId, int $width, int $height, string $format, array $conversionData = []): bool
    {
        $cacheKey = self::QUEUE_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        $queueData = [
            'image_id' => $imageId,
            'width' => $width,
            'height' => $height,
            'format' => $format,
            'queued_at' => time(),
            'data' => $conversionData
        ];
        
        return $this->wpService->wpCacheSet($cacheKey, $queueData, self::CACHE_GROUP, 3600);
    }

    /**
     * Get queued conversions for background processing
     */
    public function getQueuedConversions(int $limit = 10): array
    {
        // This is a simplified implementation - in a real scenario,
        // you might want to use a proper queue system
        return [];
    }

    /**
     * Check if a conversion is queued for background processing
     */
    public function isQueuedForConversion(int $imageId, int $width, int $height, string $format): bool
    {
        $cacheKey = self::QUEUE_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        $queueData = $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
        
        return $queueData !== false;
    }

    /**
     * Clear all conversion cache for a specific image
     */
    public function clearImageCache(int $imageId): bool
    {
        // Clear runtime cache entries for this image
        foreach (self::$runtimeCache as $key => $value) {
            if (strpos($key, (string)$imageId . '_') !== false) {
                unset(self::$runtimeCache[$key]);
            }
        }
        
        // Note: WordPress cache doesn't provide a wildcard delete function
        // In a real implementation, you might want to track cache keys
        // or use a different caching solution that supports pattern deletion
        
        return true;
    }
}