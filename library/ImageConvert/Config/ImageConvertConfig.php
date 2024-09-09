<?php

namespace Municipio\ImageConvert\Config;

use WpService\Contracts\ApplyFilters;
use Municipio\ImageConvert\Config\ImageConvertConfigInterface;

class ImageConvertConfig implements ImageConvertConfigInterface
{

  const FILTER_PREFIX = 'Municipio/ImageConvert';

  public function __construct(private ApplyFilters $wpService){}

  /**
   * If the image conversion is enabled.
   */
  public function isEnabled() : bool
  {
    return $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
      true
    );
  }

  /**
   * The maximum image dimension for image conversion.
   */
  public function maxImageDimension() : int 
  {
    return $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
      2500
    );
  }

  /**
   * The default image dimensions. 
   * If image dimensions cannot be found. 
   */
  public function defaultImageDimensions() : array
  {
    return $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
      [1280, 720]
    );
  }

  /**
   * The priority to run ImageConvert on.
   */
  public function imageDownsizePriority() : int
  {
    return $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
      1
    );
  }

  /**
   * The mime types that should be considered for image conversion.
   */
  public function mimeTypes() : array
  {
    return $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
      [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/tiff',
        'image/webp',
      ]
    );
  }

  /**
   * The suffixes for the mime types.
   * 
   * @return array
   */
  public function fileNameSuffixes() : array
  {
    $mimeTypes = $this->mimeTypes();

    return array_map(function($mime) {
      return str_replace('image/', '', $mime);
    }, $mimeTypes);

    //Add jpg as an alias for jpeg
    if(in_array('jpeg', $mimeTypes)) {
      $mimeTypes[] = 'jpg';
    }

    return $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
      $mimeTypes
    );
  }

  /**
   * The internal filter priority for image conversion.
   * 
   * This is the prority that the internal filters will hook into.
   * 
   * @return object
   */
  public function internalFilterPriority(): object
  {
    return (object) $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
        [
        'normalizeImageSize' => 10,
        'resolveMissingImageSize' => 20,
        'imageConvert' => 30,
      ]
    );
  }

  /**
   * Create a prefix for image conversion filter.
   * 
   * TODO: Move this to COMMON. It is not a config.
   * 
   * @return string
   */
  public function createFilterKey(string $filter = "") : string
  {
    return self::FILTER_PREFIX . "/" . ucfirst($filter);
  }
}
