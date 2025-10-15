<?php

namespace Modularity\Integrations\Component;

use \ComponentLibrary\Integrations\Image\ImageResolverInterface;

class ImageResolver implements ImageResolverInterface {
  
  /**
   * Get image url
   * 
   * @param int $id
   * @param array $size
   * @return string|null
   */
  public function getImageUrl(int $id, array $size): ?string {

    //Make 0 values in array false 
    $size = array_map(function($value) {
      return $value === 0 ? false : $value; 
    }, $size);

    $image = wp_get_attachment_image_src($id, $size); 
    if($image !== false && isset($image[0]) && filter_var($image[0], FILTER_VALIDATE_URL)) {
      return $image[0]; 
    }
    return null; 
  }

  /**
   * Get image alt
   * 
   * @param int $id
   * @return string|null
   */
  public function getImageAltText(int $id): ?string {
    $alt = get_post_meta($id, '_wp_attachment_image_alt', true); 
    if($alt) {
      return $alt; 
    }
    return null;
  }
}