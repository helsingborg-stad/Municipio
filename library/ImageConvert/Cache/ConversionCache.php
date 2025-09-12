<?php

namespace Municipio\ImageConvert\Cache;

use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Contracts\WpCacheDelete;
use WpService\Contracts\ApplyFilters;
use Municipio\ImageConvert\Config\ImageConvertConfigInterface;

enum ConversionStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Success    = 'success';
    case Failed     = 'failed';
}

/**
 * ConversionCache
 *
 * Manages caching and deduplication for image conversion operations.
 * Provides mechanisms to prevent duplicate processing and cache conversion results.
 */
class ConversionCache
{
    private const CACHE_GROUP   = 'municipio_image_convert';
    private const STATUS_PREFIX = 'status_';
    private const LOCK_PREFIX   = 'lock_';
    private const DEDUPLICATION_PREFIX = 'dedup_';


    private static array $runtimeCache = [];

    public function __construct(
        private WpCacheGet&WpCacheSet&WpCacheDelete&ApplyFilters $wpService,
        private ImageConvertConfigInterface $config
    ) {
    }

    /**
     * Get conversion cache key for an image conversion request
     *
     * @param ImageContract $image
     * @return string
     */
    private function getCacheKey(ImageContract $image): string
    {
        $imageId = $image->getId();
        $width   = $image->getWidth();
        $height  = $image->getHeight();
        $format  = $this->config->intermidiateImageFormat()['suffix'];
        return sprintf('%d_%dx%d_%s', $imageId, $width, $height, $format);
    }

    /**
     * Check if a similar request has been seen recently to prevent duplicate processing
     *
     * @param ImageContract $image
     * @return bool
     */
    public function hasSeenRequestRecently(ImageContract $image): bool
    {
        $cacheKey = self::DEDUPLICATION_PREFIX . $this->getCacheKey($image);
        $lastSeen = $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
        if ($lastSeen && (time() - $lastSeen) < $this->config->requestDeduplicationWindow()) {
            return true;
        }
        $this->wpService->wpCacheSet($cacheKey, time(), self::CACHE_GROUP, $this->config->requestDeduplicationWindow());
        return false;
    }

    /**
     * Check if a conversion is currently in progress (locked)
     *
     * @param ImageContract $image
     * @return bool
     */
    public function isConversionLocked(ImageContract $image): bool
    {
        $cacheKey = self::LOCK_PREFIX . $this->getCacheKey($image);
        // Check runtime cache first
        if (isset(self::$runtimeCache[$cacheKey])) {
            return self::$runtimeCache[$cacheKey];
        }
        $isLocked                      = (bool) $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
        self::$runtimeCache[$cacheKey] = $isLocked;
        return $isLocked;
    }

    /**
     * Acquire a lock for image conversion to prevent duplicate processing
     *
     * @param ImageContract $image
     * @return bool
     */
    public function acquireConversionLock(ImageContract $image): bool
    {
        $cacheKey = self::LOCK_PREFIX . $this->getCacheKey($image);

        $acquired = $this->wpService->wpCacheSet($cacheKey, time(), self::CACHE_GROUP, $this->config->lockExpiry());
        if ($acquired) {
            self::$runtimeCache[$cacheKey] = true;
        }
        return $acquired;
    }

    /**
     * Release a conversion lock
     *
     * @param ImageContract $image
     * @return bool
     */
    public function releaseConversionLock(ImageContract $image): bool
    {
        $cacheKey = self::LOCK_PREFIX . $this->getCacheKey($image);
        unset(self::$runtimeCache[$cacheKey]);
        return $this->wpService->wpCacheDelete($cacheKey, self::CACHE_GROUP);
    }

    /**
     * Get the conversion status for an image
     *
     * @param ImageContract $image
     * @return ConversionStatus|null Null if no status is set
     */
    public function getConversionStatus(ImageContract $image): ?ConversionStatus
    {
        $cacheKey = self::STATUS_PREFIX . $this->getCacheKey($image);
        // Check runtime cache first
        if (isset(self::$runtimeCache[$cacheKey])) {
            return self::$runtimeCache[$cacheKey];
        }
        $status = $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP);
        if ($status) {
            $enumStatus                    = ConversionStatus::from($status);
            self::$runtimeCache[$cacheKey] = $enumStatus;
            return $enumStatus;
        }
        return null;
    }

    /**
     * Set the conversion status for an image
     *
     * @param ImageContract $image
     * @param ConversionStatus $status
     * @return bool True on success, false on failure
     */
    public function setConversionStatus(ImageContract $image, ConversionStatus $status): bool
    {
        $cacheKey = self::STATUS_PREFIX . $this->getCacheKey($image);
        $expiry   = match ($status) {
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
     * @param ImageContract $image
     * @return bool
     */
    public function hasRecentFailure(ImageContract $image): bool
    {
        $status = $this->getConversionStatus($image);
        return $status === ConversionStatus::Failed;
    }

    /**
     * Mark a conversion as successful
     *
     * @param ImageContract $image
     * @return bool
     */
    public function markConversionSuccess(ImageContract $image): bool
    {
        return $this->setConversionStatus($image, ConversionStatus::Success);
    }

    /**
     * Mark a conversion as failed
     *
     * @param ImageContract $image
     * @return bool
     */
    public function markConversionFailed(ImageContract $image): bool
    {
        return $this->setConversionStatus($image, ConversionStatus::Failed);
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
