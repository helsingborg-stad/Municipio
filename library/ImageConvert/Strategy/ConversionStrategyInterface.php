<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;

/**
 * Interface for image conversion strategies
 */
interface ConversionStrategyInterface
{
    /**
     * Convert an image using the specific strategy
     *
     * @param ImageContract $image The image to convert
     * @param string $format The target format (e.g., 'webp')
     * @return ImageContract|false The converted image contract or false on failure
     */
    public function convert(ImageContract $image, string $format): ImageContract|false;

    /**
     * Check if the strategy can handle the conversion
     *
     * @param ImageContract $image The image to check
     * @param string $format The target format
     * @return bool True if the strategy can handle the conversion
     */
    public function canHandle(ImageContract $image, string $format): bool;

    /**
     * Get the strategy name/identifier
     *
     * @return string
     */
    public function getName(): string;
}