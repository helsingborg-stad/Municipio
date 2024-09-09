<?php

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Common\IsSpecificImageSize;

class NormalizeImageSize implements Hookable
{
    public function __construct(private $wpService, private ImageConvertConfig $config){}

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            $this->config->createFilterKey('imageDownsize'),
            [$this, 'normalizeImageSize'],
            $this->config->internalFilterPriority()->normalizeImageSize,
            3
        );
    }

    public function normalizeImageSize($false, $id, $size): mixed
    {
        //TODO: Remove this when manual testing is done.
        if ($id != 4142782) {
            return $size;
        }

        // Check if a specific size has been requested.
        if (!IsSpecificImageSize::isSpecificImageSize($size)) {
            return $size;
        }

        // Normalize incomplete size arrays by adding false values for missing dimensions.
        $size = $this->normalizeSizeFalsy($size);

        // Normalize the size (cap dimensions and apply proportional scaling).
        $size = $this->normalizeSizeCap(
            $size,
            $this->config->maxImageDimension()
        );

        return $size;
    }

    /**
     * Normalize image dimensions to ensure they do not exceed a specified limit.
     * If one dimension is missing, scale the other dimension proportionally.
     *
     * @param array $size An array containing the width and height of the image.
     * @param int|null $limit The maximum allowed dimension (both width and height). Defaults to null (no limit).
     *
     * @return array The normalized image dimensions (integers or booleans).
     */
    public function normalizeSizeCap(array $size, ?int $limit = null): array
    {
        if ($limit === null) {
            return $size;
        }

        $width = $size[0];
        $height = $size[1];

        // If both dimensions are missing, return the original size.
        if ($width === false && $height === false) {
            return $size;
        }

        // If only one dimension is provided, apply capping and scaling if necessary.
        if ($width === false || $height === false) {
            return $this->capAndRetainMissingDimension($width, $height, $limit);
        }

        // If both dimensions are integers, apply scaling if necessary.
        return $this->applyScalingIfNeeded($width, $height, $limit);
    }

    /**
     * Cap and retain the missing dimension as false.
     * If one dimension is provided and the other is false, retain the false and cap/scale the provided dimension.
     *
     * @param int|false $width The width of the image.
     * @param int|false $height The height of the image.
     * @param int $limit The maximum allowed dimension.
     *
     * @return array The normalized dimensions (with integers or booleans).
     */
    private function capAndRetainMissingDimension($width, $height, int $limit): array
    {
        // If width is false, cap height if necessary and return false for width.
        if ($width === false && is_int($height)) {
            $cappedHeight = $this->capDimension($height, $limit);
            return [false, $cappedHeight];
        }

        // If height is false, cap width if necessary and return false for height.
        if ($height === false && is_int($width)) {
            $cappedWidth = $this->capDimension($width, $limit);
            return [$cappedWidth, false];
        }

        return [$width, $height];
    }

    /**
     * Apply scaling proportionally if one or both dimensions exceed the limit.
     *
     * @param int|false $width The width of the image.
     * @param int|false $height The height of the image.
     * @param int $limit The maximum allowed dimension.
     *
     * @return array The scaled dimensions (with integers or booleans).
     */
    private function applyScalingIfNeeded($width, $height, int $limit): array
    {
        // If both dimensions are integers, check for scaling.
        if (is_int($width) && is_int($height)) {
            return $this->scaleDimensions($width, $height, $limit);
        }

        // If either dimension is false, return the size without scaling.
        return [$width, $height];
    }

    /**
     * Scale both dimensions proportionally if one exceeds the limit.
     *
     * @param int $width The width of the image.
     * @param int $height The height of the image.
     * @param int $limit The maximum allowed dimension.
     *
     * @return array The scaled width and height (with integers or booleans).
     */
    private function scaleDimensions(int $width, int $height, int $limit): array
    {
        if ($width > $limit || $height > $limit) {
            $scaleFactor = min($limit / $width, $limit / $height);

            $newWidth = (int) round($width * $scaleFactor);
            $newHeight = (int) round($height * $scaleFactor);

            return [$newWidth, $newHeight];
        }

        return [$width, $height];
    }

    /**
     * Cap a single dimension to ensure it does not exceed the limit.
     *
     * @param int $dimension The dimension to cap.
     * @param int $limit The maximum allowed size.
     *
     * @return int The capped dimension.
     */
    private function capDimension(int $dimension, int $limit): int
    {
        return min($dimension, $limit);
    }

    /**
     * Normalize an array of size values by converting non-numeric or empty values to false and casting the rest to integers.
     *
     * @param array $size An array of size values to normalize.
     *
     * @return array The normalized array where non-numeric and empty values are replaced with booleans or integers.
     */
    public function normalizeSizeFalsy(array $size): array
    {
        return array_map(function ($value) {
            if (!is_numeric($value) || (int) $value === 0) {
                return false;
            }
            return (int) $value;
        }, $size);
    }
}