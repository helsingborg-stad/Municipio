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
    private const META_KEY_LAST_CONVERSION = '_image_convert_last_conversion';

    /**
     * @inheritDoc
     */
    public function __construct(
        private ImageProcessor $imageProcessor
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ImageContract $image): ImageContract|false
    {
        if (!$this->shouldRunImageConvert()) {
            return $image;
        }
        $processedImage = $this->imageProcessor->process($image);
        $this->hasRanImageConvert();
        return $processedImage;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'runtime';
    }

    /**
     * Check if the current page has already triggered image conversion
     * since the last post update.
     *
     * @return bool False if conversion has already been triggered or is older than 24 hours, true otherwise.
     */
    public function shouldRunImageConvert(): bool
    {
        $lastConversionMade = (int) get_post_meta(get_the_ID(), self::META_KEY_LAST_CONVERSION, true);
        $lastPostUpdate     = (int) get_post_modified_time('U', true, get_the_ID());
        if ($lastConversionMade < (time() - 86400)) {
            return true;
        }
        return $lastConversionMade < $lastPostUpdate;
    }

    /**
     * Mark the current page as having triggered image conversion.
     *
     * This sets a meta field on the current post with the current timestamp.
     */
    public function hasRanImageConvert(): void
    {
        update_post_meta(get_the_ID(), self::META_KEY_LAST_CONVERSION, time());
    }
}
