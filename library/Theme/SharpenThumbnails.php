<?php

/**
 * Sharpen Thumbnails
 * 
 * Makes thumbnails sharper. This class conflicts with s3 buckets. Why?
 * 
 */

namespace Municipio\Theme;

use Municipio\Helper\File as FileHelper;
use WP_Error;

class SharpenThumbnails
{
    private $imageQuality = 92;

    public function __construct() 
    {
      //Respect image quality
      $this->imageQuality = apply_filters('jpeg_quality', $this->imageQuality, 'image_resize');

      //if(!defined('S3_UPLOADS_BUCKET')) {
          add_filter('image_make_intermediate_size', array($this, 'sharpenThumbnail'), 900);
      //}
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

      echo "TEST"; 

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
      $image->unsharpMaskImage(0, 0.5, 200.5, 0);

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