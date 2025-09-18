<?php

namespace Municipio\ImageConvert\Config;

interface ImageConvertConfigInterface
{
    public function isEnabled(): bool;
    public function imageDownsizePriority(): int;
    public function mimeTypes(): array;
    public function internalFilterPriority(): object;
    public function fileNameSuffixes(): array;
    public function maxImageDimension(): int;
    public function intermidiateImageFormat(): array;
    public function intermidiateImageQuality(): int;
    public function maxSourceFileSize(): int;
    public function failedCacheExpiry(): int;
    public function successCacheExpiry(): int;
    public function lockExpiry(): int;
    public function requestDeduplicationWindow(): int;
    public function defaultCacheExpiry(): int;
    public function pageCacheExpiry(): int;
    public function getImageConversionStrategy(): ?string;
    public function getDefaultImageConversionLogWriter(): ?string;
}
