<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\AddFilter;
use WpService\Contracts\DoAction;

/**
 * WP CLI Conversion Strategy
 * 
 * Designed for batch processing of image conversions via WP CLI commands.
 * Optimized for large-scale conversions with progress reporting and efficient
 * resource management. Ideal for maintenance tasks and bulk operations.
 * Conforms to action namespace Municipio/ImageConvert/Convert.
 */
class WpCliConversionStrategy implements ConversionStrategyInterface
{
    use WpCliHelperTrait;
    use BaseConversionTrait;
    public function __construct(
        private WpGetImageEditor&IsWpError&WpGetAttachmentMetadata&WpAttachmentIs&AddFilter&DoAction $wpService,
        private ImageConvertConfig $config,
        private ConversionCache $conversionCache
    ) {
    }

    public function process(ImageContract $image): ImageContract|false
    {
        $imageId = $image->getId();
        $width = $image->getWidth();
        $height = $image->getHeight();
        
        // Use the configured intermediate image format for consistency
        $format = 'webp'; // Should be from config but keeping simple for now

        // Check if already resized and cached
        if ($this->hasSuccessfulConversion($imageId, $width, $height, $format)) {
            // Return original image since resizing was already successful
            return $image;
        }

        // Check if conversion failed recently
        if ($this->conversionCache->hasRecentFailure($imageId, $width, $height, $format)) {
            // For CLI, we might want to retry even recent failures with verbose output
            $this->wpCliWarning("Retrying recently failed resize for image {$imageId}");
        }

        // Lock conversion to prevent duplicates
        if (!$this->lockConversion($imageId, $width, $height, $format)) {
            $this->wpCliWarning("Resize already in progress for image {$imageId}");
            return false;
        }

        try {
            // Trigger the conversion action using the specified namespace
            $conversionData = [
                'image_id' => $imageId,
                'width' => $width,
                'height' => $height,
                'format' => $format,
                'original_url' => $image->getUrl(),
                'original_path' => $image->getPath(),
                'intermediate_location' => $image->getIntermidiateLocation($format),
                'strategy' => 'wpcli'
            ];

            $this->wpService->doAction(
                'Municipio/ImageConvert/Convert',
                $conversionData
            );

            // Perform the actual conversion
            $convertedImage = $this->performConversion($image, $format);

            if ($convertedImage) {
                // Cache successful conversion
                $this->cacheSuccessfulConversion($imageId, $width, $height, $format, $convertedImage);
                
                $this->wpCliSuccess("Successfully converted image {$imageId} to {$format}");
                
                return $convertedImage;
            } else {
                // Cache failed conversion
                $this->cacheFailedConversion($imageId, $width, $height, $format);
                
                $this->wpCliError("Failed to convert image {$imageId} to {$format}");
                
                return false;
            }

        } catch (\Throwable $e) {
            // Cache failed conversion
            $this->cacheFailedConversion($imageId, $width, $height, $format);
            
            $this->wpCliError("Exception during conversion of image {$imageId}: " . $e->getMessage());
            
            error_log("WP CLI image conversion error: " . $e->getMessage());
            return false;

        } finally {
            // Always unlock conversion
            $this->unlockConversion($imageId, $width, $height, $format);
        }
    }

    public function canHandle(ImageContract $image): bool
    {
        // WP CLI strategy can handle any image resize request
        // It's particularly good for batch operations
        return true;
    }

    public function getName(): string
    {
        return 'wpcli';
    }

    /**
     * Perform the actual image conversion
     * 
     * @param ImageContract $image
     * @param string $format
     * @return ImageContract|false
     */
    private function performConversion(ImageContract $image, string $format): ImageContract|false
    {
        // Get WordPress image editor
        $editor = $this->wpService->wpGetImageEditor($image->getPath());
        
        if ($this->wpService->isWpError($editor)) {
            return false;
        }

        // Resize if needed
        if ($image->getWidth() > 0 && $image->getHeight() > 0) {
            $resizeResult = $editor->resize($image->getWidth(), $image->getHeight(), false);
            if ($this->wpService->isWpError($resizeResult)) {
                return false;
            }
        }

        // Save in the new format
        $outputPath = $image->getIntermidiateLocation($format);
        
        // Ensure directory exists
        $outputDir = dirname($outputPath);
        if (!file_exists($outputDir)) {
            wp_mkdir_p($outputDir);
        }

        $saveResult = $editor->save($outputPath, $this->getMimeType($format));
        
        if ($this->wpService->isWpError($saveResult)) {
            return false;
        }

        // Create new ImageContract for converted image
        return $this->createImageWithNewPath($image, $outputPath);
    }


}