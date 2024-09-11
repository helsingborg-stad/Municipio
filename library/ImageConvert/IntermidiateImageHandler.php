<?php 

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\AddFilter;
use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;

class IntermidiateImageHandler implements Hookable
{
    public function __construct(private AddFilter $wpService, private ImageConvertConfig $config) {}

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

        $convertedImage = $this->convertImage($image);

        if ($convertedImage) {
            return $convertedImage;
        }

        return false; // Return false on conversion failure
    }

    /**
     * Convert the given image to the format defined in the config (e.g., WebP)
     *
     * @param ImageContract $image
     * @return ImageContract|bool Array with 'path' and 'url' or false on failure
     */
    private function convertImage(ImageContract $image): ImageContract|bool
    {
        $sourceFilePath = $image->getPath(); // Use the path from the image contract
        $targetFormatSuffix = $this->config->intermidiateImageFormat()['suffix'];
        $targetFormatMime   = $this->config->intermidiateImageFormat()['mime'];
        $intermediateLocation = $image->getIntermidiateLocation($targetFormatSuffix); // Returns path and url

        // Check if ImageMagick or GD is available
        if (extension_loaded('imagick') || extension_loaded('gd')) {
            $imageEditor = wp_get_image_editor($sourceFilePath);

            if (!is_wp_error($imageEditor)) {
                // Attempt to save the image in the target format (e.g., WebP or another format)
                $savedImage = $imageEditor->save($intermediateLocation['path'], $targetFormatMime);

                if (!is_wp_error($savedImage) && file_exists($savedImage['path'])) {
                    // Set the new path and URL to the image object
                    $image->setUrl($intermediateLocation['url']);
                    $image->setPath($intermediateLocation['path']);

                    return $image; // Return the updated image object
                } else {
                    $this->imageConversionError('Error saving image as ' . $targetFormatSuffix . ': ' . $savedImage->get_error_message(), $image);
                }
            } else {
                $this->imageConversionError('Error creating image editor: ' . $imageEditor->get_error_message(), $image);
            }
        } else {
            $this->imageConversionError('Neither Imagick nor GD extension is loaded.', $image);
        }

        return false; // Conversion failed
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