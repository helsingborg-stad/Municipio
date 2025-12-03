<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ImageProcessor;

/**
 * Runtime Conversion Strategy
 *
 * Performs image conversion immediately during the request.
 * This strategy processes images synchronously using the shared ImageProcessor.
 */
class RuntimeConversionStrategy implements ConversionStrategyInterface
{
    public function __construct(
        private ImageProcessor $imageProcessor
    ) {
    }

    public function process(ImageContract $image): ImageContract|false
    {
        return $this->imageProcessor->process($image);
    }

    public function getName(): string
    {
        return 'runtime';
    }
}
