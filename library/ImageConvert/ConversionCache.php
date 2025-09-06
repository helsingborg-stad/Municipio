<?php

namespace Municipio\ImageConvert;

use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpCacheDelete;
use WpService\Contracts\ApplyFilters;

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
    private const QUEUE_INDEX_KEY = 'queue_index';
    
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
        private WpCacheGet&WpCacheSet&WpCacheDelete&ApplyFilters $wpService
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
     * Get filterable cache expiry time for failed conversions
     */
    private function getFailedCacheExpiry(): int
    {
        return (int) $this->wpService->applyFilters(
            'Municipio/ImageConvert/Config/FailedCacheExpiry',
            self::FAILED_CACHE_EXPIRY
        );
    }

    /**
     * Get filterable cache expiry time for successful conversions
     */
    private function getSuccessCacheExpiry(): int
    {
        return (int) $this->wpService->applyFilters(
            'Municipio/ImageConvert/Config/SuccessCacheExpiry',
            self::SUCCESS_CACHE_EXPIRY
        );
    }

    /**
     * Get filterable cache expiry time for conversion locks
     */
    private function getLockExpiry(): int
    {
        return (int) $this->wpService->applyFilters(
            'Municipio/ImageConvert/Config/LockExpiry',
            self::LOCK_EXPIRY
        );
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
        $acquired = $this->wpService->wpCacheSet($cacheKey, time(), self::CACHE_GROUP, $this->getLockExpiry());
        
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
            self::STATUS_FAILED => $this->getFailedCacheExpiry(),
            self::STATUS_SUCCESS => $this->getSuccessCacheExpiry(),
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
        
        // Store the queue item
        $stored = $this->wpService->wpCacheSet($cacheKey, $queueData, self::CACHE_GROUP, 3600);
        
        if ($stored) {
            // Add to queue index for retrieval
            $this->addToQueueIndex($cacheKey);
        }
        
        return $stored;
    }

    /**
     * Get queued conversions for background processing
     */
    public function getQueuedConversions(int $limit = 10): array
    {
        // Get the queue index
        $queueIndex = $this->getQueueIndex();
        
        if (empty($queueIndex)) {
            return [];
        }
        
        $conversions = [];
        $processed = 0;
        
        foreach ($queueIndex as $cacheKey) {
            if ($processed >= $limit) {
                break;
            }
            
            // Get the queue data for this key
            $queueData = $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
            
            if ($queueData !== false) {
                $conversions[] = $queueData;
                $processed++;
            } else {
                // Queue item expired or was deleted, remove from index
                $this->removeFromQueueIndex($cacheKey);
            }
        }
        
        return $conversions;
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
     * Remove a conversion from the queue after processing
     */
    public function removeFromQueue(int $imageId, int $width, int $height, string $format): bool
    {
        $cacheKey = self::QUEUE_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        // Remove from queue index
        $this->removeFromQueueIndex($cacheKey);
        
        // Remove the actual queue data
        return $this->wpService->wpCacheDelete($cacheKey, self::CACHE_GROUP);
    }

    /**
     * Get the queue index containing all queued conversion keys
     */
    private function getQueueIndex(): array
    {
        $index = $this->wpService->wpCacheGet(self::QUEUE_INDEX_KEY, self::CACHE_GROUP);
        return is_array($index) ? $index : [];
    }

    /**
     * Add a queue key to the index
     */
    private function addToQueueIndex(string $cacheKey): void
    {
        $index = $this->getQueueIndex();
        
        if (!in_array($cacheKey, $index, true)) {
            $index[] = $cacheKey;
            $this->wpService->wpCacheSet(self::QUEUE_INDEX_KEY, $index, self::CACHE_GROUP, 3600);
        }
    }

    /**
     * Remove a queue key from the index
     */
    private function removeFromQueueIndex(string $cacheKey): void
    {
        $index = $this->getQueueIndex();
        $newIndex = array_filter($index, fn($key) => $key !== $cacheKey);
        
        // Re-index the array to maintain sequential indices
        $newIndex = array_values($newIndex);
        
        $this->wpService->wpCacheSet(self::QUEUE_INDEX_KEY, $newIndex, self::CACHE_GROUP, 3600);
    }

    /**
     * Clear all conversion cache for a specific image
     */
    public function clearImageCache(int $imageId): bool
    {
        foreach (self::$runtimeCache as $key => $value) {
            if (strpos($key, (string)$imageId . '_') !== false) {
                unset(self::$runtimeCache[$key]);
            }
        }
        return true;
    }
}