<?php

namespace Municipio\Customizer\Applicators;

class Css
{
  public function __construct() {
    add_filter('kirki_municipio_config_styles', array($this, 'filterPageWidth')); //Todo, use constant to create filter name.
  }

  public function filterPageWidth($styles) {

    var_dump($styles);

    //Todo, magic here to create css width var from multiple sources. 

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
    ], $string);
  }
}