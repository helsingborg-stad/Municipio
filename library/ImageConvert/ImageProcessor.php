<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\Helper\File;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\AddFilter;
use Municipio\ImageConvert\Cache\ConversionCache;
use Municipio\ImageConvert\Logging\Log;

/**
 * ImageProcessor
 * 
 * Handles the actual image resizing and conversion logic.
 * This is the shared component used by all conversion strategies.
 */
class ImageProcessor
{
    public function __construct(
        private WpGetImageEditor&IsWpError&WpGetAttachmentMetadata&WpAttachmentIs&AddFilter $wpService,
        private ImageConvertConfig $config,
        private ConversionCache $conversionCache,
        private Log $log
    ) {
    }

    /**
     * Process an image resize request
     *
     * @param ImageContract $image The image to resize
     * @return ImageContract|false The resized image contract or false on failure
     */
    public function process(ImageContract $image): ImageContract|false
    {
        $format  = $this->config->intermidiateImageFormat()['suffix'];

        if (!$this->conversionCache->acquireConversionLock($image)) {
            return $image;
        }

        try {
            // Check if the image can be resized
            $canConvert = $this->canConvertImage($image);
            if ($canConvert instanceof \WP_Error) {

                $this->log->log(
                    $this,
                    'Cannot convert image: ' . $canConvert->get_error_message(),
                    'warning',
                    ['image' => $image, 'format' => $format, 'reason' => $canConvert->get_error_code()]
                );

                $this->conversionCache->markConversionFailed($image);
                return false;
            }

            // Add log entry to indicate that we are starting conversion
            $this->log->log(
                $this,
                'Starting image conversion.',
                'info',
                ['image' => $image, 'format' => $format]
            );

            // Set processing limits for better resource management
            $this->increaseAllowedProcessingTime();
            $this->increaseAllowedMemoryLimit();

            // Switch to the preferred image editor based on file type
            $this->setPreferredImageEditorByFiletype($image);

            $intermediateLocation = $image->getIntermidiateLocation($format);

            $imageEditor = $this->wpService->wpGetImageEditor($image->getPath());

            if (!$this->wpService->isWpError($imageEditor)) {
                // Get the original image dimensions
                $originalSize = $imageEditor->get_size();

                // Determine target dimensions using min() to avoid upscaling
                $targetWidth  = min($image->getWidth(), $originalSize['width']);
                $targetHeight = min($image->getHeight(), $originalSize['height']);

                // Resize the image
                $imageEditor->resize($targetWidth, $targetHeight, true);

                // Attempt to save the resized image
                $savedImage = $imageEditor->save($intermediateLocation['path']);

                if (!$this->wpService->isWpError($savedImage)) {
                    $image->setUrl($intermediateLocation['url']);
                    $image->setPath($intermediateLocation['path']);
                    
                    // Mark conversion as successful
                    $this->conversionCache->markConversionSuccess($image);

                    $this->log->log(
                        $this,
                        'Successfully converted image.',
                        'info',
                        ['image' => $image, 'format' => $format]
                    );
                    
                    return $image;
                } else {
                    $this->log->log(
                        $this,
                        'Cannot convert image: ' . $savedImage->get_error_message(),
                        'warning',
                        ['image' => $image, 'format' => $format, 'reason' => $savedImage->get_error_code()]
                    );

                    $this->conversionCache->markConversionFailed($image);
                }
            } else {
                $this->log->log(
                    $this,
                    'Cannot convert image: ' . $imageEditor->get_error_message(),
                    'warning',
                    ['image' => $image, 'format' => $format, 'reason' => $imageEditor->get_error_code()]
                );

                $this->conversionCache->markConversionFailed($image);
            }

            return false;
        } finally {
            // Always release the lock, even if resizing failed
            $this->conversionCache->releaseConversionLock($image);
        }
    }

    /**
     * Check if the image can be converted based on its existence, type, and size.
     */
    private function canConvertImage(ImageContract $image): true|\WP_Error
    {
        $sourceFilePath = $image->getPath();
        $sourceFileId   = $image->getId();

        // The id cannot be empty or negative
        if (empty($sourceFileId) || $sourceFileId < 0) {
            return new \WP_Error('invalid_id', 'The image ID is empty or invalid.');
        }

        // The Source file must exist
        if (!File::fileExists($sourceFilePath)) {
            return new \WP_Error('file_not_found', 'The source image file does not exist at path: ' . $sourceFilePath);
        }

        // The image must exist in database, and be a image
        if (!$this->wpService->wpAttachmentIs('image', $sourceFileId)) {
            return new \WP_Error('not_image', 'The attachment is not recognized as an image.');
        }

        // Get attachment filesize, if exceeds max size, return error
        $sourceFileSize = $this->getSourceFileSize($sourceFileId, $sourceFilePath);
        if (!$sourceFileSize) {
            return new \WP_Error('filesize_unavailable', 'Unable to determine the file size of the source image.');
        }

        if ($sourceFileSize > $this->config->maxSourceFileSize()) {
            return new \WP_Error('file_too_large', 'The source image exceeds the maximum allowed file size of ' . $this->config->maxSourceFileSize() . ' bytes.');
        }

        return true;
    }

    /**
     * Get the size of an attachment from its metadata, with a fallback to the filesystem.
     */
    private function getSourceFileSize(int $attachmentId, string $sourceFilePath): int|false
    {
        $size = $this->wpService->wpGetAttachmentMetadata($attachmentId, 'filesize');
        if ($size) {
            return intval($size);
        }
        return filesize($sourceFilePath);
    }

    /**
     * Set the preferred image editor based on the file type.
     */
    private function setPreferredImageEditorByFiletype(ImageContract $image): void
    {
        $filePath       = $image->getPath();
        $fileNameSuffix = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileTypeMime   = match (strtolower($fileNameSuffix)) {
            'png' => 'image/png',
            default => false
        };

        $availableEditors = (
            ($fileTypeMime === 'image/png') ?
            ['WP_Image_Editor_GD', 'WP_Image_Editor_Imagick'] :
            ['WP_Image_Editor_Imagick', 'WP_Image_Editor_GD']
        );

        $this->wpService->addFilter('wp_image_editors', fn() => $availableEditors);
    }

    /**
     * Increase the allowed processing time for image conversion.
     */
    private function increaseAllowedProcessingTime(): void
    {
        $maxExecutionTime = (int) ini_get('max_execution_time');
        if ($maxExecutionTime < 300) {
            ini_set('max_execution_time', '300');
        }
    }

    /**
     * Increase the allowed memory limit for image processing.
     */
    private function increaseAllowedMemoryLimit(): void
    {
        $memoryLimit = ini_get('memory_limit');
        if ($memoryLimit < '2048M') {
            ini_set('memory_limit', '2048M');
        }
    }
}