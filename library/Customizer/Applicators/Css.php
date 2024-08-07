<?php

namespace Municipio\Customizer\Applicators;

use Kirki\Module\CSS as KirkiCSS;

class Css extends AbstractApplicator
{
  private $baseFontSize = '16px';
  public  $optionKey = 'css';

  public function __construct() {
    add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_styles', array($this, 'filterFontSize'));

    /* Disable dynamic css */
    define('KIRKI_NO_OUTPUT', true);

    /* Save dynamic css on customizer save to static value */
    add_action('customize_save_after', array($this, 'storeStaticStyles'), 60, 1);
    add_action('kirki_dynamic_css', array($this, 'renderKirkiStaticCss'));
  }

  /**
   * Calculate dynamic css on save of customizer
   * 
   * @return array
   */
  public function storeStaticStyles($manager = null) {
    $this->setStatic(
      $dynamicStyles = $this->getDynamic()
    );
    return $dynamicStyles;
  }

  /**
   * Get dynamic css from Kirki
   * 
   * @return string
   */
  private function getDynamic() {
    if($runtimeCache = $this->getRuntimeCache('cssRuntimeCache')) {
      return $runtimeCache;
    }

    return $this->setRuntimeCache(
      'cssRuntimeCache',
      $this->filterStyles(
        KirkiCSS::loop_controls(\Municipio\Customizer::KIRKI_CONFIG)
      )
    );
  }

  /**
   * Get static css from option
   * 
   * @return string
   */
  private function getStaticCss(): string
  {
    $styles = $this->getStatic();
    if($styles) {
      return $this->filterStyles(
        $styles
      );
    }
    return "";
  }

  /**
   * Get hybrid css, create static if not exists.
   * 
   * The reason we are storing static css is to be 
   * backwards compatible. Some sites may not have
   * the dynamic css stored in the database.
   * 
   * @return string
   */
  private function getHybrid(): string
  {
    $static = $this->getStaticCss();
    if (!empty($static)) {
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
    $styles = apply_filters("kirki_" . \Municipio\Customizer::KIRKI_CONFIG . "_dynamic_css", $styles);
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
