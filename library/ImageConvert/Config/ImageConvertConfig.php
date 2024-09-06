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
   * The priority for image downsize.
   */
  public function imageDownsizePriority() : int
  {
    return $this->wpService->applyFilters(
      $this->createFilterKey(__FUNCTION__), 
      10
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
   * @return object
   */
  public function internalFilterPriority(): object
  {
      return (object) [
        'normalizeImageSize' => 10,
        'imageDownsize' => 20,
        'imageConvert' => 30,
      ];
  }

  /**
   * Create a prefix for image conversion filter.
   * 
   * @return string
   */
  public function createFilterKey(string $filter = "") : string
  {
    return self::FILTER_PREFIX . "/" . $filter;
  }
}
