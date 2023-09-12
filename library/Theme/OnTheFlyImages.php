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

        add_action('loop_start', function() {
            print_r( wp_get_attachment_image_src(989, [rand(500, 600), rand(500, 600)]) );
            die;
        }); 
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

        //Get image size, if request is incomplete
        if($this->isMissingSizeArguments($requestedSize)) {

            $sourceImageDimensions = $this->getSourceImageDimensions($id);

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
        if($this->hasSufficientSizeDetails($requestedSize)) {
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

        return $downsize; //Could not resolve requested size
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
        if($imageMeta = wp_get_attachment_metadata($id)) {
            if(is_array($imageMeta) && !empty($imageMeta)) {
                return $this->normalizeSizeFalsy([
                    'width' => $imageMeta['width']?? false,
                    'height' => $imageMeta['height']?? false
                ]);
            }
        }

        //Cold not get image size, not an image? 
        return new WP_Error(
            'imagesize_not_found', 
            __('Original image size could not be found, or its metadata could not be retrieved from the database.'), 
            $imageMeta
        );
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
                error_log("On The Fly Images: Created " . $requestedImageName['path']); 
                return $requestedImageName['url'];
            }

        } catch (\Exception $e) {
            if(isset($_GET['debug'])) {
                throw $e; 
            } else {
                error_log($e);
            }
        }

        //Error
        error_log("On The Fly Images: Could not create " . $requestedImageName['path']); 

        // Fallback to full size
        return $requestedImageName['sourceUrl'];
    }
}
