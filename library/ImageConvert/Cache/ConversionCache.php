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
    private const KEY_INDEX_PREFIX = 'keys_';


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
            $this->registerPersistentKey($image->getId(), $cacheKey);
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
        $stored = $this->wpService->wpCacheSet($cacheKey, $status->value, self::CACHE_GROUP, $expiry);
        if ($stored) {
            $this->registerPersistentKey($image->getId(), $cacheKey);
        }
        return $stored;
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
     * Check whether this intermediate image was successfully published.
     */
    public function hasRecentSuccess(ImageContract $image): bool
    {
        return $this->getConversionStatus($image) === ConversionStatus::Success;
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
        $indexKey = self::KEY_INDEX_PREFIX . $imageId;
        $persistentKeys = $this->wpService->wpCacheGet($indexKey, self::CACHE_GROUP);
        $runtimeKeys = self::$runtimeCache[$indexKey] ?? [];
        $registeredKeys = array_unique(array_merge(
            is_array($persistentKeys) ? $persistentKeys : [],
            is_array($runtimeKeys) ? $runtimeKeys : [],
        ));

        if ($registeredKeys !== []) {
            foreach (array_unique($registeredKeys) as $cacheKey) {
                if (is_string($cacheKey)) {
                    $this->wpService->wpCacheDelete($cacheKey, self::CACHE_GROUP);
                    unset(self::$runtimeCache[$cacheKey]);
                }
            }
        }

        $this->wpService->wpCacheDelete($indexKey, self::CACHE_GROUP);

        $statusPrefix = self::STATUS_PREFIX . $imageId . '_';
        $lockPrefix = self::LOCK_PREFIX . $imageId . '_';
        foreach (array_keys(self::$runtimeCache) as $key) {
            if (str_starts_with($key, $statusPrefix) || str_starts_with($key, $lockPrefix) || $key === $indexKey) {
                unset(self::$runtimeCache[$key]);
            }
        }

        return true;
    }

    /**
     * Keep a persistent list of cache keys so all dimensions and formats for an
     * attachment can be invalidated without scanning the object cache.
     */
    private function registerPersistentKey(int $imageId, string $cacheKey): void
    {
        $indexKey = self::KEY_INDEX_PREFIX . $imageId;
        $persistentKeys = $this->wpService->wpCacheGet($indexKey, self::CACHE_GROUP);
        $runtimeKeys = self::$runtimeCache[$indexKey] ?? [];
        $registeredKeys = array_unique(array_merge(
            is_array($persistentKeys) ? $persistentKeys : [],
            is_array($runtimeKeys) ? $runtimeKeys : [],
        ));

        if (!in_array($cacheKey, $registeredKeys, true)) {
            $registeredKeys[] = $cacheKey;
        }

        self::$runtimeCache[$indexKey] = $registeredKeys;

        $this->wpService->wpCacheSet(
            $indexKey,
            $registeredKeys,
            self::CACHE_GROUP,
            max(
                $this->config->successCacheExpiry(),
                $this->config->failedCacheExpiry(),
                $this->config->lockExpiry(),
            ),
        );
    }
}
