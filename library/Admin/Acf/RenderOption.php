<?php

namespace Municipio\Admin\Acf;

class RenderOption {
  
  /**
   * Add filter to specified fields
   */
  public function __construct() {
    add_filter('acf/render_field_settings', array($this, 'addRenderOption'));
    add_filter('acf/render_field_settings', array($this, 'addShareOption'));
  }

  public function addRenderOption($field) 
  {
    acf_render_field_setting($field, array(
      'label'			    => __('Customizer: Filter or Variable', 'municipio'),
      'instructions'	=> __('Handle as filter or css variable in customizer. Only applies to options with customizer location setting.', 'municipio'),
      'name'			    => 'render_type',
      'type'			    => 'select',
      'choices'       => array(
        ''              => __('Not selected', 'municipio'),
        'filter'        => __('Filter', 'municipio'),
        'var'           => __('Css variable', 'municipio'),
        'var_colorgroup'=> __('Css variable in colorgroup (can only be applied on groups [color, alpha])', 'municipio'),
      ),
      'ui'			      => 0,
    ), true);

    acf_render_field_setting($field, array(
      'label'			    => __('Customizer: Filter context', 'municipio'),
      'instructions'	=> __('Municipio adds context strings to different places. to affect one of these, limit context here. For multiple context use comma separator.', 'municipio'),
      'name'			    => 'filter_context',
      'type'			    => 'text',
      'ui'			      => 1,
    ), true);
  }

  public function addShareOption($field) 
  {
    acf_render_field_setting($field, array(
      'label'			    => __('Customizer: Share option', 'municipio'),
      'instructions'	=> __('Share this option with the municipio community.', 'municipio'),
      'name'			    => 'share_option',
      'type'			    => 'true_false',
      'ui'			      => 1,
    ), true);
  }
}