<?php

namespace Municipio\Customizer;

class InitCustomizerPanels
{

  /**
   * Configuration for subpanels
   *
   * @var array
   */
  private $configuration = [
    'component' => [
      [
        'id' => 'card', 
        'title' => "Card", 
        'active' => true
      ]
    ],
    'modifiers' => [
      [
        'id' => 'posts', 
        'title' => "Posts", 
        'active' => true
      ],
      [
        'id' => 'contacts', 
        'title' => "Contacts", 
        'active' => true
      ],
      [
        'id' => 'inlay', 
        'title' => "Inlay", 
        'active' => true
      ],
      [
        'id' => 'map', 
        'title' => "Map", 
        'active' => true
      ],
      [
        'id' => 'text', 
        'title' => "Text", 
        'active' => true
      ],
      [
        'id' => 'video', 
        'title' => "Video", 
        'active' => true
      ],
      [
        'id' => 'event', 
        'title' => "Event", 
        'active' => true
      ],
      [
        'id' => 'jsonrender', 
        'title' => "Json Render",
        'active' => true
      ],
      [
        'id' => 'form', 
        'title' => "Form",
        'active' => true
      ],
      [
        'id' => 'index', 
        'title' => "Index",
        'active' => true
      ],
      [
        'id' => 'localevent', 
        'title' => "Local Event",
        'active' => true
      ],
      [
        'id' => 'sectionssplit', 
        'title' => "Sections Split",
        'active' => true
      ],
      [
        'id' => 'script', 
        'title' => "Script",
        'active' => true
      ]
    ],
    'design' => [
      [
          'id' => 'general', 
          'title' => "General", 
          'active' => true
      ],
      [
          'id' => 'font', 
          'title' => "Fonts", 
          'active' => false
      ],
      [
          'id' => 'color', 
          'title' => "Colors", 
          'active' => true
      ],
      [
          'id' => 'radius', 
          'title' => "Radius", 
          'description' => '',
          'active' => true
      ],
      [
          'id' => 'width', 
          'title' => "Widths", 
          'description' => '',
          'active' => true
      ],
      [
          'id' => 'padding', 
          'title' => "Padding", 
          'description' => '',
          'active' => true
      ],
      [
          'id' => 'borders', 
          'title' => "Borders", 
          'active' => false
      ],
      [
          'id' => 'shadows', 
          'title' => "Shadows", 
          'active' => false
      ],
      [
          'id' => 'header', 
          'title' => "Header", 
          'active' => true
      ],
      [
          'id' => 'footer', 
          'title' => "Footer", 
          'active' => false
      ],
      [
          'id' => 'article', 
          'title' => "Article", 
          'active' => false
      ],
      [
          'id' => 'mobilemenu',
          'title' => "Mobile Menu", 
          'active' => true
      ]
    ]
  ];

  public function __construct() {

    //Adds panels to customizer area 
    add_action('init', array($this, 'initComponentPanel'));
    add_action('init', array($this, 'initDesignPanel'));
    add_action('init', array($this, 'initModifierPanel'));

    add_action('wp_head', array($this, 'wphead'));
  }

  /**
   * TEST
   *
   * @return void
   */
  public function wphead() {
    var_dump($this->mapFieldConfiguration($this->getFlatConfiguration())); 
  }

  /**
   * Parses the acf config
   * @return \WP_Error|void
   */
  public function mapFieldConfiguration($configuration, $dataFieldStack = [])
  { 
    $themeMods = \Municipio\Helper\CustomizeGet::get(); 

    if (is_array($configuration) && !empty($configuration)) {
        
        foreach ($configuration as $configurationKey => $config) {

            //File path
            $configFile = MUNICIPIO_PATH . 'library/AcfFields/json/customizer-' . $config['id'] . '.json';

            //Read file
            if (file_exists($configFile) && $data = json_decode(file_get_contents($configFile))) {

                //File validation
                if (count($data) != 1) {
                    return new \WP_Error("Configuration file should not contain more than one group " . $config);
                }

                //Get first group
                $data = array_pop($data);

                //Validate that we have fields 
                if (isset($data->fields) && !empty($data->fields)) {
                    foreach ($data->fields as $fieldIndex => $field) {

                        // If field is a group, set default value as array with key values
                        if($field->type === "group") {
                            $field->default_value = array();

                            foreach ($field->sub_fields as $subfield) {
                                $field->default_value[$subfield->name] = $subfield->default_value;
                            }
                        }

                        $dataFieldStack[$config['id']][$fieldIndex] = [
                            $field->key => [
                                'name' => str_replace(['municipio_', '_'], ['', '-'], $field->name),
                                'default' => $field->default_value ?? '',
                                'value' => $themeMods[$field->key] ?? '',
                                'prepend' => $field->prepend ?? null,
                                'append' => $field->append ?? null,
                                'share' => $field->share_option ?? false,
                                'renderType' => $field->render_type ?? null,
                                'fieldType' => $field->type ?? null
                            ]
                        ];
                    }
                }
            }
        }
    }

    return $dataFieldStack; 
  }

  /**
   * Get a flat version of the configuration
   * @return array
   */
  private function getFlatConfiguration($allOptions = []) {
    if(is_array($this->configuration) && !empty($this->configuration)) {
      foreach($this->configuration as $section) {
        $allOptions = array_merge($allOptions, $section); 
      }
    }
    return $allOptions; 
  }

  /**
   * Determine if panels should be registered
   *
   * @return boolean
   */
  private function shouldRegisterPanels() {
    if(!is_customize_preview() && !is_admin()) {
      return false; 
    }
    return true; 
  }

  /**
   * Inits components panel structure.
   * @return void
   */
  public function initComponentPanel()
  {
    if(!$this->shouldRegisterPanels()) {
      return; 
    }

    new \Municipio\Helper\CustomizeCreate(
        [
            'id' => 'component', 
            'title' => __('Components', 'municipio'),
        ],
        $this->configuration['component']
    );
  }

  /**
   * Inits design panel structure.
   * @return void
   */
  public function initDesignPanel()
  {
    if(!$this->shouldRegisterPanels()) {
      return; 
    }
    
    new \Municipio\Helper\CustomizeCreate(
      [
        'id' => 'design', 
        'title' => __('Design', 'municipio')
      ],
      $this->configuration['design']
    );
  }

  /**
   * Inits modifiers panel structure.
   * @return void
   */
  public function initModifierPanel()
  {
    if(!$this->shouldRegisterPanels()) {
      return; 
    }
    
    new \Municipio\Helper\CustomizeCreate(
      [
        'id' => 'modifiers', 
        'title' => __('Modifiers', 'municipio')
      ],
      $this->configuration['modifiers']
    );
  }

}