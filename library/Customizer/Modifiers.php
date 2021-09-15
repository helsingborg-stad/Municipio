<?php

namespace Municipio\Customizer;

/**
 * Class Design
 * @package Municipio\Customizer
 */
class Modifiers
{

  /**
   * @var array|null[]
   */
  private $configuration = null;  //Fill in construct due to translations

  public function __construct() {

    /**
     * Field configuration is always 
     * feteched by filename: 'customizer-{$id}.json' in 
     * MUNICIPIO_PATH . 'library/AcfFields/json/
     */
    $this->configuration = [
      [
        'id' => 'modules', 
        'title' => "Modules", 
        'description' => __('General module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ]
    ]; 

    //Init panel
    add_action('init', array($this, 'initPanel'));

    //Get configurations
    add_action('wp_head', array($this, 'getAcfCustomizerFields'), 5);

    //Add module classes
    add_action('wp_head', array($this, 'moduleClasses'), 20);
  }

  /**
   * Inits a new panel structure.
   * @return void
   */
  public function initPanel()
  {
    //Only init panels incustomizer
    if(!is_customize_preview() && !is_admin()) {
        return false; 
    }

    //Add panels & fields 
    new \Municipio\Helper\CustomizeCreate(
        [
            'id' => 'modules', 
            'title' => __('Modules', 'municipio')
        ],
        $this->configuration
    );
  }

  /**
   * Store acf fields in stack
   */
  public function getAcfCustomizerFields() {
    $this->dataFieldStack = \Municipio\Helper\CustomizeGet::getAcfCustomizerFields(
        $this->configuration
    );
  }

  /* Add options specified in customizer for modules */
  public function moduleClasses() {
      
      $moduleData = [];

      $dataStack  = array_merge(
        $this->dataFieldStack['modules'], 
        $this->dataFieldStack['site']
      );

      //Build array with context and it's classes
      if(is_array($dataStack) && !empty($dataStack)) {
          foreach($dataStack as $data) {
              foreach ($data as $key => $value) {
                  
                  //Get named parts
                  $nameParts = explode('-', $value['name']);

                  //Remove last element if array only has one value
                  if(count($nameParts) > 1) {
                      array_pop($nameParts);
                  }

                  //Create key parts
                  $Module = isset($nameParts[0]) ? $nameParts[0] : '';
                  $View   = isset($nameParts[1]) ? ucfirst($nameParts[1]) : '';

                  //Set value for key parts
                  $moduleData[$Module . $View] = !empty($value['value']) ? $value['value'] : $value['default'];
              
              }
          }
      }

      //Build filters
      $filters = [
          'ComponentLibrary/Component/Header/Modifier',
          'ComponentLibrary/Component/Card/Modifier',
          'ComponentLibrary/Component/Segment/Modifier'
      ];

      //Apply filter + function
      if(is_array($filters) && !empty($filters)) {
          foreach($filters as $filter) {
              add_filter($filter, function($modifiers, $contexts) use($moduleData) {
                  
                  if(!is_array($modifiers)) {
                      $modifiers = [];
                  }
      
                  //Always handle as array
                  if(!is_array($contexts)) {
                      $contexts = array_filter([$contexts]); 
                  }
      
                  //Create modifiers if exists
                  if(is_array($contexts) && !empty($contexts)) {
                      foreach($contexts as $key => $context) {

                          if(isset($moduleData[$context])) {
                              
                              if(!is_array($moduleData[$context])) {
                                  $moduleData[$context] = [$moduleData[$context]];
                              }
              
                              $modifiers = array_merge($modifiers, $moduleData[$context]);
                          }

                      }
                  }
      
                  return (array) $modifiers;
                  
              }, 10, 2); 
          }
      }
  }   
}