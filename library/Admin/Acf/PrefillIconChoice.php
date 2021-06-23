<?php

namespace Municipio\Admin\Acf;

class PrefillIconChoice {
  
  /**
   * Add filter to specified fields
   */
  public function __construct() {
    add_filter('acf/load_field/name=menu_item_icon', array($this, 'addIconsList'));
  }

  /**
   * Add list to dropdown
   *
   * @param array $field  Field definition
   * 
   * @return array $field Field definition with choices
   */
  public function addIconsList($field) : array 
  {
    
    $choices = \Municipio\Helper\Icons::getIcons();

    if(is_array($choices) && !empty($choices)) {
      foreach($choices as $choice) {
          $field['choices'][ $choice ] = '<i class="material-icons" style="float: left;">'. $choice .'</i> <span style="height: 24px; display: inline-block; line-height: 24px; margin-left: 8px;">'. $choice . '</span>';
      }
    } else {
      $field['choices'] = []; 
    }

    return $field; 
  }
}