<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\AddFilter;
use WpService\Contracts\IsWpError;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\Helper\File;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\WpUploadDir;

class IntermidiateImageHandler implements Hookable
{
    public function __construct(private AddFilter&isWpError&WpGetImageEditor&WpUploadDir $wpService, private ImageConvertConfig $config)
    {
    }

    public function addHooks(): void
    {
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
        $sourceFilePath       = $image->getPath();
        $targetFormatSuffix   = $this->config->intermidiateImageFormat()['suffix'];
        $targetFormatMime     = $this->config->intermidiateImageFormat()['mime'];
        $intermediateLocation = $image->getIntermidiateLocation($targetFormatSuffix);

        if ($this->config->canConvertBetweenFormats() === false) {
            // TODO: Investigate if we can avoid this file read for every image.
            $targetFormatMime     = mime_content_type($image->getPath());
            $suffix               = pathinfo($image->getPath(), PATHINFO_EXTENSION);
            $intermediateLocation = $image->getIntermidiateLocation($suffix);
        }

        // Check if the source file exists.
        if (!\Municipio\Helper\File::fileExists($sourceFilePath)) {
            return false;
        }

        $imageEditor = $this->wpService->wpGetImageEditor($sourceFilePath);

        if (!$this->wpService->isWpError($imageEditor)) {
            //Make the resize
            $imageEditor->resize(
                $image->getWidth(),
                $image->getHeight(),
                true
            );

            // Attempt to save the image in the target format and size
            $savedImage = $imageEditor->save(
                $intermediateLocation['path'],
                $targetFormatMime
            );

            if (!$this->wpService->isWpError($savedImage)) {
                $image->setUrl($intermediateLocation['url']);
                $image->setPath($intermediateLocation['path']);
                return $image;
            } else {
                $this->imageConversionError('Error saving image as ' . $targetFormatSuffix . ': ' . $savedImage->get_error_message(), $image);
            }
        } else {
            $this->imageConversionError('Error creating image editor: ' . $imageEditor->get_error_message(), $image);
        }

        return false;
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
