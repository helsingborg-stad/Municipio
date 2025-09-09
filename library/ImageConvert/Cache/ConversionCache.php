<?php

namespace Municipio\ImageConvert\Cache;

use Municipio\Helper\Image;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpCacheDelete;
use WpService\Contracts\ApplyFilters;
use Municipio\ImageConvert\Config\ImageConvertConfigInterface;

enum ConversionStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Success = 'success';
    case Failed = 'failed';
}

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
    
    
    private static array $runtimeCache = [];

    public function __construct(
        private WpCacheGet&WpCacheSet&WpCacheDelete&ApplyFilters $wpService,
        private ImageConvertConfigInterface $config
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
        $acquired = $this->wpService->wpCacheSet($cacheKey, time(), self::CACHE_GROUP, $this->config->lockExpiry());
        
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
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * @return ConversionStatus|null Null if no status is set
     * 
     */
    public function getConversionStatus(int $imageId, int $width, int $height, string $format): ?ConversionStatus
    {
        $cacheKey = self::STATUS_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        // Check runtime cache first
        if (isset(self::$runtimeCache[$cacheKey])) {
            return self::$runtimeCache[$cacheKey];
        }
        
        $status = $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
        
        if ($status) {
            $enumStatus = ConversionStatus::from($status);
            self::$runtimeCache[$cacheKey] = $enumStatus;
            return $enumStatus;
        }
        
        return null;
    }

    /**
     * Set the conversion status for an image
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * @param ConversionStatus $status
     * 
     * @return bool True on success, false on failure
     */
    public function setConversionStatus(int $imageId, int $width, int $height, string $format, ConversionStatus $status): bool
    {
        $cacheKey = self::STATUS_PREFIX . $this->getCacheKey($imageId, $width, $height, $format);
        
        $expiry = match ($status) {
            ConversionStatus::Failed        => $this->config->failedCacheExpiry(),
            ConversionStatus::Success       => $this->config->successCacheExpiry(),
            ConversionStatus::Pending       => $this->config->defaultCacheExpiry(),
            ConversionStatus::Processing    => $this->config->defaultCacheExpiry(),
            default                         => $this->config->defaultCacheExpiry(),
        };
        
        self::$runtimeCache[$cacheKey] = $status;
        
        return $this->wpService->wpCacheSet($cacheKey, $status->value, self::CACHE_GROUP, $expiry);
    }

    /**
     * Check if a conversion recently failed
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * 
     * @return bool
     */
    public function hasRecentFailure(int $imageId, int $width, int $height, string $format): bool
    {
        $status = $this->getConversionStatus($imageId, $width, $height, $format);
        return $status === ConversionStatus::Failed;
    }

    /**
     * Mark a conversion as successful
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * 
     * @return bool
     */
    public function markConversionSuccess(int $imageId, int $width, int $height, string $format): bool
    {
        return $this->setConversionStatus($imageId, $width, $height, $format, ConversionStatus::Success);
    }

    /**
     * Mark a conversion as failed
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * 
     * @return bool
     */
    public function markConversionFailed(int $imageId, int $width, int $height, string $format): bool
    {
        return $this->setConversionStatus($imageId, $width, $height, $format, ConversionStatus::Failed);
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