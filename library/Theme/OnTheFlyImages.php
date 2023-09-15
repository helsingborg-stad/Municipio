<?php

/**
 * OnTheFlyImages
 * 
 * This class adds support for auto scaled images in WordPress. This
 * class works with levraging users on load call. All images on the requested page 
 * will be generation at the FIRST visit of the page. 
 * 
 * This class also supports non-cropped images. 
 * Simply send false as the size argument, and the value will be calculated from metadata. 
 * 
 * Example: wp_get_attachment_image_src($id, [100, false]);
 * 
 */

namespace Municipio\Theme;

use Municipio\Helper\File as FileHelper;
use WP_Error;

class OnTheFlyImages
{
    private $mimes = [
        'image/jpeg', 
        'image/png', 
        'image/tiff'
    ];

    public function __construct() 
    {
        //Resizing
        add_filter('image_downsize', array($this, 'runResizeImage'), 5, 3);
    }

    /**
     * Checks if the requested size should trigger a resize operation.
     *
     * @param mixed $requestedSize The size to be checked, can be an integer, array, or other types.
     *
     * @return bool Returns true if the requested size should trigger a resize, false otherwise.
     */
    private function shouldResize($requestedSize) : bool {
        if(is_array($requestedSize)) {
            return true;
        }
        return false;
    }

    /* Hook to image resize function
     *
     * @param  int     $downsize      Always false (do not try to find an alternative).
     * @param  int     $id            Image ID
     * @param  int     $size          An ARRAY($width, $height)
     *
     * @return string|bool            URL of resized image, false if error
     */
    public function runResizeImage($downsize, $id, $requestedSize)
    {
        //Is valid id?
        if(!is_numeric($id)) {
            return $downsize;
        }

        //This is not a request for resize
        if(!$this->shouldResize($requestedSize)) {
            return $downsize;
        }

        //Verify that this is an valid image type
        if(!$this->isImageMime($id)) {
            return $downsize;
        }
        
        //Normalize requested size
        $requestedSize = $this->normalizeSizeFalsy($requestedSize);

        //Get the source image dimensions
        $sourceImageDimensions = $this->getSourceImageDimensions($id);

        //Source image is large enough
        if(!is_wp_error($sourceImageDimensions) && !$this->isSourceImageSufficientSize($sourceImageDimensions, $requestedSize)) {
            $scriptUri = isset($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : "unknown url.";  
            $this->debug("The source image is not large enough to resize to the requested size (". implode("x", $requestedSize) ." from " . implode("x", $sourceImageDimensions)  . ") [" . wp_get_attachment_image_src($id, 'full')[0] . " on " . $scriptUri . "]");
            return $downsize;
        }

        //Get image size, if request is incomplete
        if($this->isMissingSizeArguments($requestedSize)) {

            if(!is_wp_error($sourceImageDimensions)) {
                $requestedSize = $this->calculateMissingDimension(
                    $requestedSize[0], 
                    $requestedSize[1], 
                    $sourceImageDimensions
                );

                if ($requestedSize !== false) {
                    $requestedSize = $this->normalizeSizeCap($requestedSize); 
                }
            }
        }

        //Has fixed width & height
        if(!$this->hasSufficientSizeDetails($requestedSize)) {
            if(is_wp_error($sourceImageDimensions)) {
                $this->debug("Could not calculate the requested size " . implode('x', $requestedSize));
            } else {
                $this->debug("Could not calculate the requested size " . implode('x', $requestedSize) . " from . implode('x', $sourceImageDimensions) ");
            }
            return $downsize;
        }

        return [
            $this->resizeImage(
                $id, 
                $requestedSize, 
                true
            ),
            $requestedSize[0],
            $requestedSize[1],
            true
        ];
    }

    /**
     * Create a unique image name for a requested size based on the given image ID.
     *
     * This function generates unique image file names for resized versions of the original image
     * based on the requested width and height dimensions.
     *
     * @param int $id The ID of the original image.
     * @param array $requestedSize An array containing the requested width and height dimensions.
     *
     * @return array An associative array containing the following keys:
     *               - 'url': The URL of the resized image.
     *               - 'path': The server file path of the resized image.
     *               - 'sourceUrl': The URL of the original image.
     */
    private function createRequestedImageName($id, $requestedSize) {

        //Get size
        list($width, $height) = array_values($requestedSize);

        // Get upload directory info
        $uploadInfo = wp_upload_dir();
        $uploadDir  = $uploadInfo['basedir'];
        $uploadUrl  = $uploadInfo['baseurl'];

        // Get file path info
        $path       = get_attached_file($id);
        $pathInfo   = pathinfo($path);

        //Create variants
        $ext            = isset($pathInfo['extension']) && !empty($pathInfo['extension']) ? $pathInfo['extension'] : '';
        $pathRelative   = str_replace(array($uploadDir, ".$ext" ), '', $path);
        $suffix         = "{$width}x{$height}";
        $path           = "{$uploadDir}{$pathRelative}-{$suffix}.{$ext}";
        $url            = "{$uploadUrl}{$pathRelative}-{$suffix}.{$ext}";
        $sourcePath     = "{$uploadDir}{$pathRelative}.{$ext}";
        $sourceUrl      = "{$uploadUrl}{$pathRelative}.{$ext}";

        //Return details
        return [
            'url' => $url,
            'path' => $path,
            'sourceUrl' => $sourceUrl,
            'sourcePath' => $sourcePath,
        ];

    }

    /**
     * Checks if the provided requested size details are sufficient for resizing.
     *
     * This function verifies whether the requested size details, provided as an array containing
     * width and height dimensions, are both set, numeric, and non-empty. It determines if the
     * dimensions are sufficient for resizing operations.
     *
     * @param array $requestedSize An array containing the requested width and height dimensions.
     *
     * @return bool Returns true if both width and height dimensions are set, numeric, and non-empty;
     *              otherwise, returns false.
     */
    private function hasSufficientSizeDetails(array $requestedSize) : bool {
        for($i = 0; $i < 2; $i++) {
            if(!isset($requestedSize[$i]) || !is_numeric($requestedSize[$i]) || empty($requestedSize[$i])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a given post or attachment has an image MIME type.
     *
     * This function retrieves the MIME type of the provided post or attachment ID
     * and checks if it belongs to the allowed image MIME types.
     *
     * @param int $id The ID of the post or attachment to check.
     *
     * @return bool Returns true if the provided attachment is an image with an
     *              allowed MIME type, or false otherwise.
     */
    private function isImageMime(int $id) : bool {

        $mime = get_post_mime_type($id);

        if(!is_string($mime)) {
            return false;
        }

        if (!in_array($mime, $this->mimes)) {
            return false;
        }

        return true;
    }

        
    /**
     * Calculate the missing dimension based on available values.
     *
     * @param int|false $width  The width dimension (or false if missing).
     * @param int|false $height The height dimension (or false if missing).
     * @param array     $sourceImageDimensions An array containing source image dimensions.
     *
     * @return array|false An array containing width and height, or false if both dimensions are missing.
     */
    private function calculateMissingDimension($width, $height, $sourceImageDimensions) {
        list($srcWidth, $srcHeight) = array_values($sourceImageDimensions);

        if(empty($width)) {
            $width = false;
        }

        if(empty($height)) {
            $height = false;
        }

        if ($width === false && $height !== false) {
            // Width is missing, calculate it
            $width = round(($height / $srcHeight) * $srcWidth);
        } elseif ($width !== false && $height === false) {
            // Height is missing, calculate it
            $height = round(($width / $srcWidth) * $srcHeight);
        } elseif ($width === false && $height === false) {
            $height = 500;
            $width = 500;
        }

        return [
            (int) $width, 
            (int) $height
        ];
    }

    /**
     * Retrieve the dimensions of the source image.
     *
     * This method takes an attachment ID, retrieves the file path associated with that ID, and then
     * attempts to obtain the dimensions (width and height) of the image file. If successful, it returns
     * an array containing the width and height. If the image size cannot be determined, it returns a
     * WP_Error object with appropriate error messages.
     *
     * @param int $id The attachment ID of the source image.
     *
     * @return array|WP_Error An array containing 'width' and 'height' keys if dimensions are found,
     *                        or a WP_Error object if there was an issue retrieving the dimensions.
     */
    private function getSourceImageDimensions(int $id) {
        if($imageMeta = wp_get_attachment_metadata($id, true)) {
            if(is_array($imageMeta) && !empty($imageMeta)) {
                return $this->normalizeSizeFalsy([
                    'width' => $imageMeta['width']?? false,
                    'height' => $imageMeta['height']?? false
                ]);
            }
        }

        //If not image meta is found, try to repair it.
        if(empty($imageMeta)) {
            if($imageMeta = $this->createImageMeta($id)) {
                return $this->normalizeSizeFalsy([
                    'width' => $imageMeta['width']?? false,
                    'height' => $imageMeta['height']?? false
                ]);
            }
        }

        //Cold not get image size, not an image? 
        return new WP_Error(
            'imagesize_not_found', 
            __('Original image size could not be found in this images metarecords.'), 
            $imageMeta
        );
    }

    /**
     * Create image metadata for the specified attachment ID.
     *
     * This function retrieves the image file associated with the given attachment ID and attempts to
     * extract its dimensions to update the attachment's metadata. If successful, it returns an array
     * with the image's width and height; otherwise, it returns false.
     *
     * @param int $id The attachment ID for which to create image metadata.
     *
     * @return array|false An array containing 'width' and 'height' keys with image dimensions if successful, or false if unsuccessful.
     */
    private function createImageMeta($id) {

        if($image = get_attached_file($id, true)) {
            if($size = FileHelper::getImageSize($image)) {
                $this->debug("Original image size could not be found, creating new metarecords.");

                wp_update_attachment_metadata($id, [
                    'width' => $size[0],
                    'height' => $size[1],
                ]);

                return [
                    'width' => $size[0],
                    'height' => $size[1],
                ]; 
            }
        }

        $this->debug("Original image size could not be found in this images metarecords. We tried to create them, but failed. This may be due to a missing file.");

        return false;
    }

    /**
     * Check if the source image's dimensions are sufficient for the requested size.
     *
     * This function compares the dimensions of a source image with the requested size
     * and returns true if both the width and height of the source image are greater than
     * the corresponding dimensions in the requested size.
     *
     * @param array $sourceImageDimensions An array containing the dimensions of the source image, where
     *                                    the first element is the width and the second element is the height.
     * @param array $requestedSize An array containing the requested dimensions, where the first element is
     *                            the requested width and the second element is the requested height.
     *
     * @return bool Returns true if the source image is large enough for the requested size, otherwise false.
     */
    private function isSourceImageSufficientSize($sourceImageDimensions, $requestedSize) {
        if($sourceImageDimensions['width'] < $requestedSize[0]) {
            return false; 
        }
        if($sourceImageDimensions['height'] < $requestedSize[1]) {
            return false; 
        }
        return true;
    }

    /**
     * Check if all required size arguments are provided.
     *
     * This method checks if an array contains exactly two non-empty elements. It is typically used to
     * determine if both width and height dimensions are provided for an image size.
     *
     * @param array $size An array containing size-related arguments.
     *
     * @return bool Returns true if both width and height are provided, false otherwise.
     */
    private function isMissingSizeArguments($size) {
        if(count(array_filter($size)) == 2) {
            return false;
        }
        return true;
    }

    /**
     * Normalize image dimensions to prevent them from exceeding a specified limit.
     *
     * This method takes an array of image dimensions (width and height) and ensures
     * that neither dimension exceeds the specified limit. If a dimension is greater
     * than the limit, it will be set to the limit.
     *
     * @param array $size An array containing the width and height of the image.
     * @param int   $limit The maximum allowed dimension (both width and height).
     *
     * @return array The normalized image dimensions.
     */
    private function normalizeSizeCap(array $size, int $limit = 2500) : array {
        array_walk($size, function(&$value) use ($limit) {
            $value = (int) ((int) $value > $limit) ? $limit : $value;
        });
        return $size;
    }

    /**
     * Normalize an array of size values by converting non-numeric and empty values to false and casting the rest to integers.
     *
     * This function takes an array of size values, typically representing width and height,
     * and normalizes them by performing the following operations:
     *
     * - If a value is not numeric, it is set to false.
     * - If a value is 0 (considered as falsy), it is set to false.
     *
     * @param array $size An array of size values to normalize.
     *
     * @return array The normalized array where non-numeric and empty values are replaced with false.
     */
    private function normalizeSizeFalsy(array $size) : array {
        array_walk($size, function(&$value) {
            if(!is_numeric($value)) {
                $value = false;
            }

            if(is_numeric($value) && $value == 0) {
                $value = false;
            }

            if($value !== false) {
                $value = (int) $value;
            }
        });

        return $size;
    }

    /* Resize image on the fly
     *
     * @param  int     $attachment_id Attachment ID
     * @param  int     $width         Width
     * @param  int     $height        Height
     * @param  boolean $crop          Crop or not
     *
     * @return string|bool            URL of resized image, false if error
     */
    public function resizeImage($id, $requestedSize, $crop = true)
    {
        //Create name
        $requestedImageName = $this->createRequestedImageName($id, $requestedSize); 

        // If file exists: do nothing
        if (FileHelper::fileExists($requestedImageName['path'])) {
            return $requestedImageName['url'];
        }

        // Generate thumbnail
        try {

            $intermidiateSize = image_make_intermediate_size(
                $requestedImageName['sourcePath'], 
                $requestedSize[0],
                $requestedSize[1],
                $crop
            );

            if ($intermidiateSize) {
                $this->debug("Created " . $requestedImageName['path']);
                return $requestedImageName['url'];
            }

        } catch (\Exception $e) {
            if(isset($_GET['debug'])) {
                throw $e; 
            } else {
                $this->debug($e);
            }
        }

        //Error
        $this->debug("Could not create " . $requestedImageName['path']);

        // Fallback to full size
        return $requestedImageName['sourceUrl'];
    }

    /**
     * Log debugging information to the error log.
     *
     * This function is used to log debugging messages and optional metadata to the error log.
     * Debugging messages will only be logged if the 'MUNUCIPIO_DEBUG_OTFI' constant is defined.
     *
     * @param string $message The main debugging message to log.
     *
     * @return void
     */
    private function debug($message) {
        if(!defined('MUNUCIPIO_DEBUG_OTFI')) {
            return; 
        }
        
        $messageParts = [
            'Municipio On The Fly Images:',
            $message
        ];

        error_log(str_replace("\n", "", implode(" ", $messageParts)));
    } 
}
