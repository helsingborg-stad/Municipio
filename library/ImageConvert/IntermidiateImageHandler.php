<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\AddFilter;
use WpService\Contracts\IsWpError;
use WpService\Contracts\IsAdmin;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\Helper\File;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\WpUploadDir;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;

class IntermidiateImageHandler implements Hookable
{
    public function __construct(private AddFilter&isWpError&WpGetImageEditor&WpUploadDir&WpGetAttachmentMetadata&IsAdmin&WpAttachmentIs $wpService, private ImageConvertConfig $config)
    {
    }

    public function addHooks(): void
    {
        if ($this->wpService->isAdmin()) {
            return;
        }

        $this->wpService->addFilter(
            $this->config->createFilterKey('imageDownsize'),
            [$this, 'createIntermidiateImage'],
            $this->config->internalFilterPriority()->intermidiateImageConvert,
            1
        );
    }

    /**
     * Create intermediate image and set new URL and path
     *
     * @param ImageContract $image
     * @return ImageContract|bool
     */
    public function createIntermidiateImage($image): ImageContract|bool
    {
        if (!$image instanceof ImageContract) {
            return $image; // Fallback to original if not an instance of ImageContract
        }

        //Deliver the image if it already exists
        $intermediateLocation = $image->getIntermidiateLocation(
            $this->config->intermidiateImageFormat()['suffix']
        );

        //Check if the intermediate image already exists, if so return it
        //This is to avoid unnecessary image conversions
        //but will affect perfomance in environments connected
        //to an object storage like S3 or OpenStack Swift
        //this file exist is cached indefinitely if found,
        //and will not be checked again util cache flush.
        if (File::fileExists($intermediateLocation['path'])) {
            $image->setUrl($intermediateLocation['url']);
            $image->setPath($intermediateLocation['path']);
            return $image;
        }

        //Create the intermediate image replacement if not exists
        return $this->convertImage($image);
    }

    /**
     * Convert the given image to the format defined in the config (e.g., WebP)
     *
     * @param ImageContract $image
     * @return ImageContract|bool Array with 'path' and 'url' or false on failure
     */
    private function convertImage(ImageContract $image): ImageContract|false
    {
        if (!$this->canConvertImage($image, $this->config)) {
            $this->imageConversionError('Image conversion is not possible from the source file. The image may not exist, be too large, or lacking the relevant metadata', $image);
            return false;
        }

        $intermediateLocation = $image->getIntermidiateLocation(
            $this->config->intermidiateImageFormat()['suffix']
        );

        $imageEditor = $this->wpService->wpGetImageEditor(
            $image->getPath()
        );

        if (!$this->wpService->isWpError($imageEditor)) {
            // Get the original image dimensions
            $originalSize = $imageEditor->get_size();
            
            // Determine target dimensions using min() to avoid upscaling
            $targetWidth = min($image->getWidth(), $originalSize['width']);
            $targetHeight = min($image->getHeight(), $originalSize['height']);

            // Resize the image
            $imageEditor->resize($targetWidth, $targetHeight, true);

            // Attempt to save the image in the target format and size
            $savedImage = $imageEditor->save($intermediateLocation['path']);

            if (!$this->wpService->isWpError($savedImage)) {
                $image->setUrl($intermediateLocation['url']);
                $image->setPath($intermediateLocation['path']);
                return $image;
            } else {
                $this->imageConversionError('Error saving image: ' . $savedImage->get_error_message(), $image);
            }
        } else {
            $this->imageConversionError('Error creating image editor: ' . $imageEditor->get_error_message(), $image);
        }

        return false;
    }

    private function canConvertImage(ImageContract $image, ImageConvertConfig $config): bool
    {
        //Get image details
        $sourceFilePath = $image->getPath();
        $sourceFileId   = $image->getId();

        // The Source file must exist
        if (!\Municipio\Helper\File::fileExists($sourceFilePath)) {
            return false;
        }

        // The image must exist in database, and be a image
        if (!$this->wpService->wpAttachmentIs('image', $sourceFileId)) {
            return false;
        }

        //Get attachment filesize, if exceeds max size, return false
        $sourceFileSize = $this->getSourceFileSize($sourceFileId, $sourceFilePath);
        if (!$sourceFileSize || ($sourceFileSize > $config->maxSourceFileSize())) {
            return false;
        }

        return true;
    }

    /**
     * Get the size of an attachment from its metadata, with a fallback to the filesystem.
     *
     * @param int $attachmentId The attachment ID.
     * @param string $sourceFilePath The path to the source file.
     *
     * @return int|false The size of the attachment in bytes, or false if the file does not exist. With a warning.
     */
    private function getSourceFileSize($attachmentId, $sourceFilePath): int|false
    {
        $size = $this->wpService->wpGetAttachmentMetadata($attachmentId, 'filesize');
        if ($size) {
            return intval($size);
        }
        return filesize($sourceFilePath);
    }

    /**
     * Get the value of a meta key from the metadata of an attachment.
     *
     * @param int $attachmentId The attachment ID.
     * @param string $metaKey The meta key to search for.
     *
     * @return mixed The value of the meta key, or false if the key was not found.
     */
    private function getAttachmentMetaData($attachmentId, $metaKey): mixed
    {
        $metaData = $this->wpService->wpGetAttachmentMetadata($attachmentId);
        if ($metaData !== false) {
            if ($result = $this->searchKeyRecursively($metaKey, $metaData)) {
                return $result;
            }
        }
        return false;
    }

    /**
     * Search for a key in a multidimensional array recursively.
     *
     * @param string $metaKey The key to search for.
     * @param array $metaData The array to search in.
     *
     * @return mixed|null The value of the key if found, otherwise null.
     */
    private function searchKeyRecursively($metaKey, $metaData)
    {
        if (array_key_exists($metaKey, $metaData)) {
            return $metaData[$metaKey];
        }

        foreach ($metaData as $key => $value) {
            // If the value is an array, search recursively
            if (is_array($value)) {
                $result = $this->searchKeyRecursively($metaKey, $value);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Logs an image conversion error with detailed info.
     *
     * @param string $message
     * @param ImageContract $image
     * @return void
     */
    private function imageConversionError(string $message, ImageContract $image): void
    {
        error_log('Image conversion error for Image ID: ' . $image->getId() . '. Message: ' . $message);
    }
}
