<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;

/**
 * Interface for image resizing strategies
 * 
 * Defines how and when images should be resized to requested dimensions.
 * Strategies determine the processing approach: immediate, background, mixed, or CLI.
 */
interface ConversionStrategyInterface
{
    /**
     * Process an image resize request using the specific strategy
     *
     * @param ImageContract $image The image to resize
     * @return ImageContract|false The resized image contract or false if strategy queues for later
     */
    public function process(ImageContract $image): ImageContract|false;

    /**
     * Check if the strategy can handle the image resize request
     *
     * @param ImageContract $image The image to check
     * @return bool True if the strategy can handle the request
     */
    public function canHandle(ImageContract $image): bool;

    /**
     * Get the strategy name/identifier
     *
     * @return string
     */
    public function getName(): string;
}