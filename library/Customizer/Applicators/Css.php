<?php

namespace Municipio\Customizer\Applicators;

class Css
{
  public function __construct() {
    add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_styles', array($this, 'filterPageWidth'));
  }

  /**
   * Handle custom page width depending on page type
   *
   * @param array $styles Default styles array with separate width element
   * @return array $styles Styles array with container width
   */
  public function filterPageWidth($styles) {

    //Pop width
    if(isset($styles['global']['width'])) {
      $width = $styles['global']['width'];
      unset($styles['global']['width']); 
    }

    //Add content
    $styles['global'][':root']['--container-width-content'] = $width['content'];

    //Determine & add container width
    if(is_front_page()||is_home()) {
      $styles['global'][':root']['--container-width'] = $width['frontpage'];
    } elseif(is_archive()) {
      $styles['global'][':root']['--container-width'] = $width['archive'];
    } else {
      $styles['global'][':root']['--container-width'] = $width['default'];
    }
 
    return $styles; 
  }
}