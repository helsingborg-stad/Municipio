<?php

namespace Municipio\Theme;

class OnTheFlyImages
{
    public function __construct()
    {
        add_filter('image_downsize', array($this, 'runResizeImage'), 5, 3);
        add_filter('image_resize_dimensions', array($this, 'upscaleThumbnail'), 10, 6);
        add_filter('image_make_intermediate_size', array($this, 'sharpenThumbnail'), 900);
    }

    /* Hook to image resize function
     *
     * @param  int     $downsize      Always false (do not try to find an alternative).
     * @param  int     $id            Image ID
     * @param  int     $size          An ARRAY($width, $height)
     *
     * @return string|bool            URL of resized image, false if error
     */
    public function runResizeImage($downsize, $id, $size)
    {
        if (is_array($size) && count($size) == 2 && !empty($id)) {

            //Check for image
            if (!in_array(get_post_mime_type($id), array('image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/x-icon'))) {
                return false;
            }

            //Get attachmentmeta
            if (!is_numeric($size[0]) ||!is_numeric($size[1])) {
                $attachmentMetaData = wp_get_attachment_metadata($id);
            }

            //Check that we have the needed data to make calculations
            if (array_filter($size)) {

                //Calc height (from width)
                if (!is_numeric($size[0])) {
                    $scale = $size[1] / $attachmentMetaData['height'];
                    $size[0] = floor($attachmentMetaData['width'] * $scale);
                }

                //Calc width (from height)
                if (!is_numeric($size[1])) {
                    $scale = $size[0] / $attachmentMetaData['width'];
                    $size[1] = floor($attachmentMetaData['height'] * $scale);
                }
            } else {
                return false;
            }

            //Normalize size (do not create humungous images)
            if ($size[0] > 2500) {
                $size[0] = 2500;
            }

            //Normalize size (do not create humungous images)
            if ($size[1] > 2500) {
                $size[1] = 2500;
            }

            return array(
                $this->resizeImage($id, $size[0], $size[1], true),
                $size[0],
                $size[1],
                true
            );
        }

        return false;
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
        if (file_exists($dest_path)) {
            return $url;
        }

        // Generate thumbnail
        if (image_make_intermediate_size($path, $width, $height, $crop)) {
            return $url;
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
     * @return string $resizedFile  The new image file as resized variant.
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
        $image->unsharpMaskImage(0, 0.5, 1, 0);

        // Store the JPG file, with as default a compression quality of 92 (default WordPress = 90, default ImageMagick = 85...)
        $image->setImageFormat("jpg");
        $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality(92);
        $image->writeImage($resizedFile);

        // Remove the JPG from memory
        $image->destroy();

        //Return sharpened image
        return $resizedFile;
    }
}
