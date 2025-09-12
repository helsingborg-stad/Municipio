<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Config\ImageConvertConfigInterface;
use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ImageProcessor;
use WpService\WpService;

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
        private ImageProcessor $imageProcessor,
        private WpService $wpService,
        private ImageConvertConfigInterface $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ImageContract $image): ImageContract|false
    {
        if ($this->shouldRunImageConvert() === false) {
            return $image;
        }
        $processedImage = $this->imageProcessor->process($image);
        $this->markAsRanImageConvert();

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
        if ($this->config->useReducedModeForImageConversionRuntimeStrategy() === false) {
            return true;
        }

        //Always run if we are logged in
        if ($this->wpService->isUserLoggedIn()) {
            return true;
        }

        $lastConversionMade = (int) $this->wpService->getPostMeta(
            $this->wpService->getTheID(),
            self::META_KEY_LAST_CONVERSION,
            true
        );
        $lastPostUpdate     = (int) $this->wpService->getPostModifiedTime('U', true, $this->wpService->getTheID());

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
    public function markAsRanImageConvert(): void
    {
        if ($this->config->useReducedModeForImageConversionRuntimeStrategy() === false) {
            return;
        }

        $this->wpService->updatePostMeta(
            $this->wpService->getTheID(),
            self::META_KEY_LAST_CONVERSION,
            time()
        );
    }
}
