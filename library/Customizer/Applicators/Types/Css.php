<?php 

namespace Municipio\Customizer\Applicators\Types;

use Municipio\Customizer\Applicators\AbstractApplicator;
use Municipio\Customizer\Applicators\ApplicatorInterface;
use WpService\WpService;
use Error;
use Kirki\Module\CSS as KirkiCSS;

class Css extends AbstractApplicator implements ApplicatorInterface {
  
  private $baseFontSize = '16px';
  public  $optionName = 'css';
  private static $hasApplied = false;
  private $kirkiConfigName;

  public function __construct(private WpService $wpService){
    define('KIRKI_NO_OUTPUT', true);
    $this->kirkiConfigName = \Municipio\Customizer::KIRKI_CONFIG;
  }

  public function getKey(): string
  {
    return 'css';
  }

  /**
   * Apply data to css with the kirki filter
   * 
   * @param array|object|string $data
   * 
   * @return void
   */
  public function applyData(array|object|string $data): void
  {
    $this->wpService->addFilter('kirki_' . $this->kirkiConfigName . '_styles', [$this, 'filterFontSize']);
    $this->wpService->addAction('kirki_dynamic_css', function() use ($data) {
      if(!self::$hasApplied) {
        echo $data;
      }
      self::$hasApplied = true;
    }, 20);
  }

  /**
   * Get css styles from Kirki
   * 
   * @return string
   */
  public function getData(): string
  {
    return $this->getCssOutputComment() . $this->filterStyles(
      KirkiCSS::loop_controls($this->kirkiConfigName)
    );
  }

  /**
   * Get css output comment
   * This is a comment that is added to the top of the css output 
   * cached in this functionality.
   */
  private function getCssOutputComment(): string
  {
    return implode(PHP_EOL, [
      '',
      '/*',
      ' * Cached CSS Styles @ ' . date('Y-m-d H:i:s'),
      ' */',
      ''
    ]);
  }

  /**
   * Filter styles.
   * 
   * Applies necessary filters and sanitizes the styles.
   *
   * @param string $styles The raw styles to filter.
   * @return string|false The filtered styles or false if empty.
   */
  private function filterStyles(string $styles)
  {
    //Native filter
    $filteredStyles = $this->wpService->applyFilters(
      "kirki_" . $this->kirkiConfigName . "_dynamic_css",
      $styles
    );

    //Additional filters (escape, strip tags, etc)
    $filteredStyles = wp_strip_all_tags($filteredStyles);
    $filteredStyles = preg_replace('/\s+/', ' ', $filteredStyles);
    $filteredStyles = $filteredStyles . "\n";

    //Return filtered styles, if any.
    return !empty($filteredStyles) ? $filteredStyles : false;
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