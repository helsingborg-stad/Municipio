<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ImageProcessor;
use Spatie\Async\Pool;

/**
 * Async Conversion Strategy
 * 
 * Performs image conversion asynchronously using parallel child processes.
 * Uses spatie/async to process images in parallel without blocking the main request.
 * This strategy provides immediate parallel processing rather than deferred processing.
 * 
 * TODO: Must paralellize conversions. If pool exists, new should join existing pool.
 */
class AsyncConversionStrategy implements ConversionStrategyInterface
{
    public function __construct(
        private ImageProcessor $imageProcessor
    ) {
    }

    public function process(ImageContract $image): ImageContract|false
    {
        // Create async pool for parallel processing
        $pool = Pool::create();
        
        $result = null;
        $error = null;
        
        // Add the image processing task to the async pool
        $pool
            ->add(function () use ($image) {
                // Process the image using the shared processor
                return $this->imageProcessor->process($image);
            })
            ->then(function ($processedImage) use (&$result) {
                $result = $processedImage;
            })
            ->catch(function ($exception) use (&$error) {
                $error = $exception;
                error_log("Async image conversion failed: " . $exception->getMessage());
            });

        // Wait for all async tasks to complete
        $pool->wait();

        // Handle errors
        if ($error !== null) {
            return false;
        }

        return $result ?: false;
    }
    
    public function getName(): string
    {
        return 'async';
    }
}