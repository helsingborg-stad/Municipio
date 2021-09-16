<?php

namespace Municipio\Admin\Acf;

class RenderOption {
  
  /**
   * Add filter to specified fields
   */
  public function __construct() {
    add_filter('acf/render_field_settings', array($this, 'addRenderOption'));
  }

  public function addRenderOption($field) 
  {
    acf_render_field_setting($field, array(
      'label'			    => __('Customizer: Filter or Variable', 'municipio'),
      'instructions'	=> __('Handle as filter or css variable in customizer. Only applies to options with customizer location setting.', 'municipio'),
      'name'			    => 'render_type',
      'type'			    => 'select',
      'choices'       => array(
        ''        => __('Not selected', 'municipio'),
        'filter'  => __('Filter', 'municipio'),
        'var'     => __('Css variable', 'municipio')
      ),
      'ui'			      => 0,
    ), true);
  }
}