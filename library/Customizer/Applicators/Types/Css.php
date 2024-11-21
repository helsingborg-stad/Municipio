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
  private $postType = 'all';
  private $option = [];

  public function __construct(private WpService $wpService){
    define('KIRKI_NO_OUTPUT', true);
  }

  public function getKey(): string
  {
    return 'css';
  }

  public function applyData(array|object|string $data)
  {
    $this->wpService->addFilter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_styles', [$this, 'filterFontSize']);
    $this->wpService->addAction('kirki_dynamic_css', function() use ($data) {
      echo $data;
    }, 500);
  }

  /**
   * Get css styles from Kirki
   * 
   * @return string
   */
  public function getData(): string
  {
    return $this->getCssOutputComment() . $this->filterStyles(
      KirkiCSS::loop_controls(\Municipio\Customizer::KIRKI_CONFIG)
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
    $filteredStyles = apply_filters(
        "kirki_" . \Municipio\Customizer::KIRKI_CONFIG . "_dynamic_css",
        $styles
    );
    $filteredStyles = wp_strip_all_tags($filteredStyles);
    $filteredStyles = preg_replace('/\s+/', ' ', $filteredStyles);
    $filteredStyles = $filteredStyles . "\n";
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