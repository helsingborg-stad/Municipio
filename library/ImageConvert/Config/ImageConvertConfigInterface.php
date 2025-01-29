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
}
