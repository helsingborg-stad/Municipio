<?php

namespace Municipio\Customizer\Applicators;

use Kirki\Module\CSS as KirkiCSS;
use Kirki\Module\CSS\Generator;

class Css
{
  private $baseFontSize = '16px';
  private $staticCssOptionKey = 'theme_mod_municipio_static_css';

  public function __construct() {
    add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_styles', array($this, 'filterFontSize'));

    /* Save dynamic css on customizer save to static value */
    add_action('customize_save_after', array($this, 'storeStaticStyles'), 50, 1);
    add_action('kirki_dynamic_css', array($this, 'renderKirkiStaticCss'));
  }

  /**
   * Calculate dynamic css on save of customizer
   * 
   * @return array
   */
  public function storeStaticStyles($manager = null) {
    $styles = $this->getDynamic();
    update_option(
        $this->staticCssOptionKey, 
        $styles
    );
    return $styles;
  }

  /**
   * Get dynamic css from Kirki
   * 
   * @return string
   */
  private function getDynamic() {
    return $this->filterStyles(
      KirkiCSS::loop_controls(\Municipio\Customizer::KIRKI_CONFIG)
    );
  }

  /**
   * Get static css from option
   * 
   * @return string
   */
  private function getStatic(): string
  {
    return $this->filterStyles(
      get_option($this->staticCssOptionKey, false)
    );
  }

  /**
   * Get hybrid css, create static if not exists
   * 
   * @return string
   */
  private function getHybrid(): string
  {
    if($static = $this->getStatic()) {
      return $static;
    }
    return $this->storeStaticStyles();
  }

  /**
   * Render static css
   * 
   * @param string $styles
   * @return void
   */
  public function renderKirkiStaticCss($styles) {
    $styles = $this->getHybrid();
    if (!empty($styles)) {
      echo $styles;
    }
  }
  
  /**
   * Filter styles
   * 
   * @param string $styles
   * @return string
   */
  private function filterStyles($styles) {
    $styles = $styles = apply_filters("kirki_" . \Municipio\Customizer::KIRKI_CONFIG . "_dynamic_css", $styles);
    $styles = wp_strip_all_tags($styles);
    if(!empty($styles)) {
      return $styles;
    }
    return false;
  }

  /**
   * Make font sizes in pixels, to rem.
   *
   * @param array $styles
   * @return array $styles
   */
  public function filterFontSize($styles): array 
  {
    $baseSize = $this->getBaseFontSize($styles['global'][':root']); 
    foreach($styles['global'][':root'] as $key => &$item) {
      if($this->canTransformValue($key, $item)) {
        $item = $this->makePxValueNumeric($item) / $baseSize . 'rem';
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
    if(strpos($key, 'font-size-base')) {
      return false; //Base should be in PX
    }
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
