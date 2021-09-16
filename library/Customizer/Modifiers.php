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
      ],
      [
        'id' => 'posts', 
        'title' => "Posts", 
        'description' => __('Posts module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'contacts', 
        'title' => "Contacts", 
        'description' => __('Contacts module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'inlay', 
        'title' => "Inlay", 
        'description' => __('Inlay module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'map', 
        'title' => "Map", 
        'description' => __('Map module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'text', 
        'title' => "Text", 
        'description' => __('Text module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'video', 
        'title' => "Video", 
        'description' => __('Video module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'event', 
        'title' => "Event", 
        'description' => __('Event module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'jsonrender', 
        'title' => "Json Render",
        'description' => __('Json render module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'form', 
        'title' => "Form",
        'description' => __('Form render module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'index', 
        'title' => "Index",
        'description' => __('Index module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'text', 
        'title' => "Text",
        'description' => __('Text module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'localevent', 
        'title' => "Local Event",
        'description' => __('Local event module settings', 'municipio'),
        'render' => false,
        'share' => true,
        'active' => true
      ],
      [
        'id' => 'sectionssplit', 
        'title' => "Sections Split",
        'description' => __('Sections, split module settings', 'municipio'),
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
            'id' => 'modifiers', 
            'title' => __('Modifiers', 'municipio')
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
      $dataStack = []; 

      if(isset($this->configuration) && !empty($this->configuration) && is_array($this->configuration)) {
        
        foreach($this->configuration as $config) {

          //Only add if allowed to render & active
          if($config['render'] !== true || $config['active'] !== true) {
            continue;
          }

          //Only add if defined
          if(!isset($this->dataFieldStack[$config['id']])) {
            continue;
          }

          //Add to data stack
          $dataStack  = array_merge(
            $dataStack,
            $this->dataFieldStack[$config['id']]
          ); 

        }

      }

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