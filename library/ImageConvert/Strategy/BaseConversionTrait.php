<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;

/**
 * Base Conversion Strategy Trait
 * 
 * Provides common functionality used across different conversion strategies.
 * Reduces code duplication and standardizes common operations.
 */
trait BaseConversionTrait
{
    /**
     * Check if a conversion has been successful recently
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * @return bool
     */
    protected function hasSuccessfulConversion(int $imageId, int $width, int $height, string $format): bool
    {
        $status = $this->conversionCache->getConversionStatus($imageId, $width, $height, $format);
        return $status === $this->conversionCache::STATUS_SUCCESS;
    }



    /**
     * Acquire a conversion lock to prevent duplicate processing
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * @return bool
     */
    protected function lockConversion(int $imageId, int $width, int $height, string $format): bool
    {
        return $this->conversionCache->acquireConversionLock($imageId, $width, $height, $format);
    }

    /**
     * Release a conversion lock
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * @return bool
     */
    protected function unlockConversion(int $imageId, int $width, int $height, string $format): bool
    {
        return $this->conversionCache->releaseConversionLock($imageId, $width, $height, $format);
    }

    /**
     * Cache a successful conversion
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * @param ImageContract $convertedImage
     * @return bool
     */
    protected function cacheSuccessfulConversion(int $imageId, int $width, int $height, string $format, ImageContract $convertedImage): bool
    {
        return $this->conversionCache->markConversionSuccess($imageId, $width, $height, $format);
    }

    /**
     * Cache a failed conversion
     * 
     * @param int $imageId
     * @param int $width
     * @param int $height
     * @param string $format
     * @return bool
     */
    protected function cacheFailedConversion(int $imageId, int $width, int $height, string $format): bool
    {
        return $this->conversionCache->markConversionFailed($imageId, $width, $height, $format);
    }

    /**
     * Create a new ImageContract with modified path and URL
     * 
     * @param ImageContract $originalImage
     * @param string $newPath
     * @return ImageContract
     */
    protected function createImageWithNewPath(ImageContract $originalImage, string $newPath): ImageContract
    {
        // Clone the original image and modify its path
        $newImage = clone $originalImage;
        $newImage->setPath($newPath);
        
        // Generate URL from path if possible
        $uploadsDir = wp_upload_dir();
        if (str_starts_with($newPath, $uploadsDir['basedir'])) {
            $relativePath = str_replace($uploadsDir['basedir'], '', $newPath);
            $newUrl = $uploadsDir['baseurl'] . $relativePath;
            $newImage->setUrl($newUrl);
        }
        
        return $newImage;
    }

    /**
     * Get MIME type for a given format
     * 
     * @param string $format
     * @return string
     */
    protected function getMimeType(string $format): string
    {
        return match (strtolower($format)) {
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'jpeg', 'jpg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'image/jpeg'
        };
    }
}