<?php

namespace Municipio\Customizer\Applicators;

class Css
{
  public function __construct() {
    add_filter('kirki_municipio_config_styles', array($this, 'filterPageWidth'));
  }

  public function filterPageWidth($styles) {

    var_dump($styles);

    return $styles; 
  }

  private function makeCssVarName($string) {
    return "--" . str_replace([
      '--',
      '_',
      'municipio_'
    ], [
      '',
      '-',
      ''
    ], $string)
  }
}