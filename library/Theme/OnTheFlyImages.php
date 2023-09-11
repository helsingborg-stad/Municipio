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
    private $imageQuality = 92;
    
    public function __construct() 
    {
        //Respect image quality
        $this->imageQuality = apply_filters('jpeg_quality', $this->imageQuality, 'image_resize');

        //Resizing
        add_filter('image_downsize', array($this, 'runResizeImage'), 5, 3);
        
        //Quality enhancements
        if(!defined('S3_UPLOADS_BUCKET')) {
            add_filter('image_resize_dimensions', array($this, 'upscaleThumbnail'), 10, 6);
            add_filter('image_make_intermediate_size', array($this, 'sharpenThumbnail'), 900);
        }
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

        //This is not a request for resize
        if(is_string($requestedSize)) {
            return $downsize;
        } else {
            $requestedSize = $this->normalizeSizeFalsy($requestedSize);
        }

        if(!$this->isImageMime($id)) {
            return $downsize;
        }

        //Get image size, if request is incomplete
        if($this->isMissingSizeArguments($requestedSize)) {

            $sourceImageDimensions = $this->getSourceImageDimensions($id);

            if(!is_wp_error($sourceImageDimensions)) {
            
                // Calculate the missing dimension
                $calculatedSize = $this->calculateMissingDimension(
                    $requestedSize[0], 
                    0, 
                    $sourceImageDimensions
                );

                if ($calculatedSize !== false) {

                    //Normalize values, if to large
                    $calculatedSize = $this->normalizeSize($calculatedSize); 

                    // Resize the image with the calculated dimensions
                    return [
                        $this->resizeImage(
                            $id,
                            $calculatedSize[0], 
                            $calculatedSize[1], 
                            true
                        ),
                        $calculatedSize[0],
                        $calculatedSize[1],
                        true
                    ];
                }
            }
        }

        //Has fixed width & height
        return [
            $this->resizeImage(
                $id, 
                $requestedSize[0], 
                $requestedSize[1], 
                true
            ),
            $requestedSize[0],
            $requestedSize[1],
            true
        ];
    }

    /**
     * Check if a given post or attachment has an image MIME type.
     *
     * This function retrieves the MIME type of the provided post or attachment ID
     * and checks if it belongs to the allowed image MIME types, including JPEG,
     * PNG, GIF, BMP, TIFF, and ICO.
     *
     * @param int $id The ID of the post or attachment to check.
     *
     * @return bool Returns true if the provided attachment is an image with an
     *              allowed MIME type, or false otherwise.
     */
    private function isImageMime($id) {
        if(!$mime = get_post_mime_type($id)) {
            $mime = FileHelper::getMimeType(
                get_attached_file($id)
            );
        }

        if (!in_array($mime, array('image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/x-icon'))) {
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
            // Both dimensions are missing, handle this case as needed
            // You can set default values or raise an error
            return false;
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
    private function getSourceImageDimensions($id) {

        //Get image sizes
        if(!$imageMeta = wp_get_attachment_metadata($id)) {
            $filePath = get_attached_file($id); 
            if(FileHelper::fileExists($filePath)) {
                $imageMeta = FileHelper::getImageSize($filePath); 
            }
        }

        //Return array with image dimensions
        if(is_array($imageMeta) && !empty($imageMeta)) {
            return $this->normalizeSizeFalsy([
                'width' => $imageMeta[0] ?? false,
                'height' => $imageMeta[1] ?? false
            ]);
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
    private function normalizeSize(array $size, int $limit = 2500) : array {
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
     * - Numeric values are typecasted to integers.
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

            $value = $value; 

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
    public function resizeImage($attachment_id, $width, $height, $crop = true)
    {
        // Get upload directory info
        $upload_info = wp_upload_dir();
        $upload_dir  = $upload_info['basedir'];
        $upload_url  = $upload_info['baseurl'];

        // Get file path info
        $path      = get_attached_file($attachment_id);
        $path_info = pathinfo($path);

        $ext       = isset($path_info['extension']) && !empty($path_info['extension']) ? $path_info['extension'] : '';
        $rel_path  = str_replace(array( $upload_dir, ".$ext" ), '', $path);
        $suffix    = "{$width}x{$height}";
        $dest_path = "{$upload_dir}{$rel_path}-{$suffix}.{$ext}";
        $url       = "{$upload_url}{$rel_path}-{$suffix}.{$ext}";

        // If file exists: do nothing
        if (FileHelper::fileExists($dest_path)) {
            return $url;
        }

        // Generate thumbnail
        try {
            if ($meta = image_make_intermediate_size($path, $width, $height, $crop)) {
                error_log("Image not found. Creating: " . $path . print_r($meta, true)); 
                return $url;
            }
        } catch (\Exception $e) {
            if(isset($_GET['debug'])) {
                throw $e; 
            } else {
                error_log($e);
            }
        }

        // Fallback to full size
        return "{$upload_url}{$rel_path}.{$ext}";
    }

    /* Upscale images when they are to small
     * Fixes issues where images get skewed due to forced ratio
     *
     * @param  int     $originalWidth        Original width
     * @param  int     $originalHeight        Original height
     * @param  int     $newWidth         New width
     * @param  int     $newHeight         New height
     * @param  bool    $crop          Crop or not
     *
     * @return Array                  Array with new dimension
     */
    public function upscaleThumbnail($default, $originalWidth, $originalHeight, $newWidth, $newHeight, $crop)
    {
        if (!$crop) {
            return null;
        } // let the wordpress default function handle this

        $sizeRatio = max($newWidth / $originalWidth, $newHeight / $originalHeight);

        $cropWidth = round($newWidth / $sizeRatio);
        $cropHeight = round($newHeight / $sizeRatio);

        $s_x = floor(($originalWidth - $cropWidth) / 2);
        $s_y = floor(($originalHeight - $cropHeight) / 2);

        if (is_array($crop)) {

            //Handles left, right and center (no change)
            if ($crop[ 0 ] === 'left') {
                $s_x = 0;
            } elseif ($crop[ 0 ] === 'right') {
                $s_x = $originalWidth - $cropWidth;
            }

            //Handles top, bottom and center (no change)
            if ($crop[ 1 ] === 'top') {
                $s_y = 0;
            } elseif ($crop[ 1 ] === 'bottom') {
                $s_y = $originalHeight - $cropHeight;
            }
        }

        return array( 0, 0, (int) $s_x, (int) $s_y, (int) $newWidth, (int) $newHeight, (int) $cropWidth, (int) $cropHeight );
    }

    /* Increase the sharpness of images to make them look crispier
     *
     * @param string $resizedFile   The image file
     * @return string $resizedFile  The new image file as sharpened variant.
     */

    public function sharpenThumbnail($resizedFile)
    {

        //Bail if imagic is missing
        if (!class_exists('Imagick')) {
            return $resizedFile;
        }

        //Create image
        $image = new \Imagick($resizedFile);

        //Get image size
        $imageSize = @getimagesize($resizedFile);
        if (!$imageSize) {
            return $resizedFile;
        }

        list($originalWidth, $originalHeight, $originalType) = $imageSize;

        //Check if JPEG
        if ($originalType != IMAGETYPE_JPEG) {
            return $resizedFile;
        }

        // Sharpen the image (the default is via the Lanczos algorithm) [Radius, Sigma, Sharpening, Threshold]
        $image->unsharpMaskImage(0, 0.5, 1.5, 0);

        // Store the JPG file, with as default a compression quality of 92 (default WordPress = 90, default ImageMagick = 85...)
        $image->setImageFormat("jpg");
        $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality($this->imageQuality);
        $image->writeImage($resizedFile);

        // Remove the JPG from memory
        $image->destroy();

        //Return sharpened image
        return $resizedFile;
    }
}
