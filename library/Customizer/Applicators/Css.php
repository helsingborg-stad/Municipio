<?php

namespace Municipio\Customizer\Applicators;

use Kirki\Module\CSS as KirkiCSS;

class Css extends AbstractApplicator
{
  private $baseFontSize = '16px';
  public  $optionName = 'css';
  private $postType = 'all';
  private $option = [];

  public function __construct() {
    add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_styles', array($this, 'filterFontSize'));

    /* Disable dynamic css */
    define('KIRKI_NO_OUTPUT', true);
    
    /* Save dynamic css on customizer save to static value */
    add_action('customize_save_after', array($this, 'resetPostTypeCache'), 60, 1);
    add_action('kirki_dynamic_css', array($this, 'renderKirkiStaticCss'));

    /* Post type specific cache */
    add_action('wp', function () {
      $this->option =  get_option($this->optionName) ?? [];
      
      if (
        !empty($this->option) && 
        (count($this->option) !== 1 && isset($this->option[$this->postType]))
      ) {
         $postType = get_post_type();
         $this->postType = isset($this->option[$postType]) ? $postType : $this->postType;
      }
    }, 10);
  }

  /**
   * Resets the post type cache option
   * Sets filter to add post type specific cache
   * 
   * @return void
   */
  public function resetPostTypeCache() {
    update_option($this->optionName, apply_filters('Municipio\Customizer\Applicators\Css\CssPostTypeCache', []));
  }

  /**
   * Calculate dynamic css on save of customizer
   * 
   * @return array
   */
  public function storeStaticStyles($manager = null) {
    $this->option[$this->postType] = $this->getDynamic();

    update_option($this->optionName, $this->option);

    return $this->option[$this->postType];
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
    $savedCss = $this->option[$this->postType] ?? null;

    if (!empty($savedCss)) {
      return $savedCss;
    }
    
    if (is_customize_preview()) {
      return $this->getDynamic();
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
