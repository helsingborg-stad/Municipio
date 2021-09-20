<?php

namespace Municipio\Customizer;

class CustomizerPanels
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
        'active' => false
      ],
      [
        'id' => 'jsonrender', 
        'title' => "Json Render",
        'active' => false
      ],
      [
        'id' => 'form', 
        'title' => "Form",
        'active' => false
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

    //Render css variables in header
    add_action('wp_head', array($this, 'renderRootCss'), 1);

    //Apply filters
    add_action('wp_head', array($this, 'triggerFilters'), 2);

    //Customization viewVars
    add_filter('Municipio/Controller/Customize', array($this, 'getCustomizationData'), 10); 
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

  /**
   * Render root css block
   *
   * @return void
   */
  public function renderRootCss() {
    $configuration  = $this->getFlatConfiguration(); 
    $dataFieldStack = $this->mapFieldConfiguration($configuration); 

    $this->renderCssVariables($configuration, $dataFieldStack); 
  }

  /**
   * Trigger filters
   * 
   * @return void
   */
  public function triggerFilters() {
    $configuration  = $this->getFlatConfiguration(); 
    $dataFieldStack = $this->mapFieldConfiguration($configuration); 

    $this->appendModifierClasses($configuration, $dataFieldStack); 
  }

  /**
   * Get customization data
   *
   * @param array $data
   * @return object
   */
  public function getCustomizationData($data) {
    $configuration  = $this->getFlatConfiguration(); 
    $dataFieldStack = $this->mapFieldConfiguration($configuration); 

    return $this->getControllerVariables($configuration, $dataFieldStack); 
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

                        //Create result stack, with all neccessary data
                        $dataFieldStack[$config['id']][$fieldIndex] = [
                            $field->key => [
                              'name' => str_replace(['municipio_', '_'], ['', '-'], $field->name),
                              'default' => $field->default_value ?? '',
                              'value' => $themeMods[$field->key] ?? '',
                              'prepend' => $field->prepend ?? null,
                              'append' => $field->append ?? null,
                              'share' => $field->share_option ?? false,
                              'renderType' => $field->render_type ?? null,
                              'fieldType' => $field->type ?? null,
                              'filterContext' => $field->filter_context ?? null
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
   * Render root css variables
   * @return void
   */
  public function renderCssVariables($configuration, $dataFieldStack)
  {

      $inlineStyle = null;

      foreach ($configuration as $configurationItem) {

          //Only add if active
          if($configurationItem['active'] !== true) {
            continue;
          }

          //Only add if defined
          if(!array_key_exists($configurationItem['id'], $dataFieldStack)) {
            continue;
          }

          //Get stack
          $stackItems = $dataFieldStack[$configurationItem['id']];

          //Init section
          $inlineStyle .= PHP_EOL . '  /* css-var: ' . $configurationItem['id'] . ' */' . PHP_EOL;

          if(is_array($stackItems) && !empty($stackItems)) {

              foreach ($stackItems as $index => $prop) {

                  $propItem   = $prop[key($stackItems[$index])];

                  //Bail out if not css var types
                  if(!in_array($propItem['renderType'], ['var', 'var_colorgroup'])) {
                    continue;
                  }

                  //Handle colorfields
                  if($propItem['fieldType'] == 'color_picker'||($propItem['fieldType'] == 'group' && $propItem['renderType'] == 'var_colorgroup')) {
                    $propItem['value'] = \Municipio\Helper\Color::prepareColor($propItem);                                    
                  }

                  //Handle width
                  if($configurationItem['id'] === 'width') {

                      if(!in_array($propItem['name'], ['container-width-content'])) {

                          //Do not render archive on any other page
                          if(!is_archive() && $propItem['name'] == "container-width-archive") {
                              continue;
                          }

                          //Do not render fontpage width on any other page
                          if((!is_front_page() && !is_home()) && $propItem['name'] == "container-width-frontpage") {
                              continue;
                          }

                          //Do not render default container width on special pagetypes
                          if((is_archive()||is_front_page()||is_home()||is_tax()) && $propItem['name'] == "container-width") {
                              continue;
                          }

                          //Use archive prop or frontpage prop as container-width
                          if(substr($propItem['name'], 0, strlen("container-width")) == "container-width") {
                              $propItem['name'] = "container-width";           
                          }

                      }
                      
                  }

                  /** Add append & prepent values, incl. defaults */
                  $inlineStyle .= \Municipio\Helper\CustomizeGet::createCssVar(
                      $propItem['name'],
                      $propItem['prepend'],
                      $propItem['value'],
                      $propItem['append'],
                      $propItem['default']                 
                  );

              }
          }
      }

      wp_dequeue_style('municipio-css-vars');
      wp_register_style('municipio-css-vars', false);
      wp_enqueue_style('municipio-css-vars');
      wp_add_inline_style('municipio-css-vars', ":root {{$inlineStyle}}");
  }

  /**
   * Add modifier filterdata to components
   *
   * @param array $configuration
   * @param array $dataFieldStack
   * @return void
   */
  public function appendModifierClasses($configuration, $dataFieldStack) {

    if(isset($configuration) && !empty($configuration) && is_array($configuration)) {

      foreach ($configuration as $configurationItem) {

        //Only add if active
        if($configurationItem['active'] !== true) {
          continue;
        }
         
        //Only add if defined
        if(!array_key_exists($configurationItem['id'], $dataFieldStack)) {
          continue;
        }

        //Get stack
        $stackItems = $dataFieldStack[$configurationItem['id']];

        if(is_array($stackItems) && !empty($stackItems)) {

          foreach ($stackItems as $index => $prop) {

            $propItem   = $prop[key($stackItems[$index])];

            //Bail out if not filter
            if(!in_array($propItem['renderType'], ['filter'])) {
              continue;
            }

            //Consolidate filter data
            $filter = [
              'value' => $propItem['value'],
              'context' => $this->parseContextString($propItem['filterContext'])
            ]; 
          
            //Add filter
            add_filter('ComponentLibrary/Component/Modifier', function($modifiers, $contexts) use ($filter) {

              if(!is_array($contexts)) {
                $contexts = [$contexts]; 
              }

              if(!is_array($modifiers)) {
                $modifiers = [$modifiers]; 
              }

              if(is_array($contexts) && !empty($contexts)) {
                foreach($contexts as $context) {
                  if(in_array($context, $filter['context'])) {
                    $modifiers[] = $filter['value'];
                  }
                }
              }

              return $modifiers;
            }, 10, 2); 
          }
        }
      }
    }
  }

  /**
   * Get controller variables
   * @return void
   */
  public function getControllerVariables($configuration, $dataFieldStack)
  {
    $returnObject = (object) []; // Declare return object

    foreach ($configuration as $configurationItem) {

      //Only add if active
      if($configurationItem['active'] !== true) {
        continue;
      }

      //Only add if defined
      if(!array_key_exists($configurationItem['id'], $dataFieldStack)) {
        continue;
      }

      //Get stack
      $stackItems = $dataFieldStack[$configurationItem['id']];

      if(is_array($stackItems) && !empty($stackItems)) {

        foreach ($stackItems as $index => $prop) {

          $propItem   = $prop[key($stackItems[$index])];

          //Bail out if not controller var types
          if(!in_array($propItem['renderType'], ['var_controller'])) {
            continue;
          }

          //Create empty object to store
          if(!isset($returnObject->{$configurationItem['id']})) {
            $returnObject->{$configurationItem['id']} = (object) []; 
          }

          //Add prop, default to val.
          $returnObject->{
            $configurationItem['id']
          }->{
            is_string($propItem['name']) ? \Municipio\Helper\FormatObject::camelCase($propItem['name']) : $propItem['name']
          } = $this->createViewVar($propItem); 

        }
      }
    }

    return $returnObject; 
  }

  /**
   * Create var with prefix, and suffix, default fallback.
   *
   * @param array $propItem
   * 
   * @return string
   */
  private function createViewVar ($propItem) {

    if(!is_null($propItem['value'])) {
      $value = $propItem['value'];
    } else {
      $value = $propItem['default']; 
    }

    //Return as string with suffix, prefix
    if(is_string($value)||is_numeric($value)) {
      return $propItem['prepend'] . (string) $value . $propItem['append'];
    }

    //Return as boolean
    if(is_bool($value)) {
      return $value; 
    }

    return null; 
  }

  /**
   * Make contexts always a arr
   *
   * @param string $contexts
   * @return void
   */
  private function parseContextString($contexts) {

    if(is_null($contexts)) {
      return []; 
    }

    //Explode contexts string
    $contexts = explode(",", $contexts);  

    //Always handle as array
    if(!is_array($contexts)) {
        $contexts = array_filter([$contexts]); 
    }

    //Trim all contexts
    return array_map('trim', $contexts);

  } 
}