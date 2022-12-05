<?php

namespace Municipio\Customizer\Applicators;

class Css
{
  private $baseFontSize = '16px';

  public function __construct() {
    add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_styles', array($this, 'filterPageWidth'));
    add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_styles', array($this, 'filterFontSize'));   
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
    } elseif(is_archive()||is_tax()) {
      $styles['global'][':root']['--container-width'] = $width['archive'];
    } else {
      $styles['global'][':root']['--container-width'] = $width['default'];
    }
 
    return $styles; 
  }

  /**
   * Make font sizes in pixels, to rm.
   *
   * @param array $styles
   * @return array $styles
   */
  public function filterFontSize($styles): array 
  {
    if(is_iterable($styles['global'][':root'])) {
      $baseSize = $this->getBaseFontSize($styles['global'][':root']); 
      foreach($styles['global'][':root'] as $key => &$item) {
        if($this->canTransformValue($key, $item)) {
          $item = $this->makePxValueNumeric($item) / $baseSize . 'em';
        }
      }
    }
    
    return $styles;
  }

  /**
   * Check if value should be transformed
   *
   * @param string $key
   * @param string $value
   * @return boolean
   */
  private function canTransformValue($key, $value): bool
  {
    if(!strpos($key, 'font')) {
      return false;
    }
    if(!strpos($value, 'px')) {
      return false; 
    }
    return true; 
  } 

  /**
   * Get the base font size
   *
   * @param array $styles
   * @return integer $baseFontSize
   */
  private function getBaseFontSize($styles): int
  {
    if(is_iterable($styles)) {
      foreach($styles as $key => $style) {
        if($key == '--font-size-base') {
          return $this->makePxValueNumeric($style); 
        }
      }
    }

    return $this->makePxValueNumeric(
      $this->baseFontSize
    );
  }

  /**
   * Transform a px valiue to integer
   *
   * @param string $value Value with unit
   * @return int $value Value without unit
   */
  private function makePxValueNumeric($value): int
  {
    $value = str_replace('px', '', $value); 

    if(is_numeric($value) && !empty($value)) {
      return (int) $value;
    }
    return (int) str_replace('px', '', $this->baseFontSize);
  }
}
