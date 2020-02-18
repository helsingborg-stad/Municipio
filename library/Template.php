<?php

namespace Municipio;

use \HelsingborgStad\GlobalBladeEngine as Blade;
use BladeComponentLibrary as ComponentLibrary;

class Template
{
  public function __construct() {
    add_action('init', array($this, 'registerViewPaths'), 10); 
    add_action('init', array($this, 'initComonentLibrary'), 15); 
    
    add_filter('template_redirect', array($this, 'addTemplateFilters'), 10);
    
    add_filter('template_include', array($this, 'sanitizeViewName'), 10);
    add_filter('template_include', array($this, 'loadViewData'), 15);
  }

  /**
   * Init the component library
   * @return void
   */
  public static function initComonentLibrary() {
    new ComponentLibrary\init();
  }

  /**
   * Register paths containing views
   * @return void
   */
  public static function registerViewPaths() {
    if($viewPaths = \Municipio\Helper\Template::getViewPaths()) {
      foreach($viewPaths as $path) {
        Blade::addViewPath(rtrim($path, DIRECTORY_SEPARATOR), true);
      }
    } else {
      wp_die("No view paths registered, please register at least one."); 
    }
  }

  /**
   * @param $view
   */
  public function sanitizeViewName($view) { 
    return $this->getViewNameFromPath($view); 
  }

  /**
   * @param $view
   * @param array $data
   */
  public function loadViewData($view, $data = array()) { 

    //Get controller data
    $viewData = $this->accessProtected(
                  $this->loadController($this->getControllerNameFromView($view)),
                  'data'
                );

    //Render the view 
    return $this->renderView(
      (string)  $view, 
      (array)   apply_filters('Municipio/blade/data', $viewData)
    );
  }

  /**
  * Loads controller for view template
  * @param  string $template Path to template
  * @return object           The controller
  */
  public function loadController($template)
  {
    //Do something before controller creation
    do_action_deprecated('Municipio/blade/before_load_controller', $template, '3.0', 'Municipio/blade/beforeLoadController'); 

    //Handle 404 renaming
    if ($template == '404') {
        $template = 'E404';
    }

    //Locate controller
    if (!$controller = \Municipio\Helper\Controller::locateController($template)) {
        $controller = \Municipio\Helper\Controller::locateController('BaseController');
    }

    //Filter 
    $controller = apply_filters('Municipio/blade/controller', $controller);

    //Require controller
    require_once $controller;
    $namespace = \Municipio\Helper\Controller::getNamespace($controller);
    $class = '\\' . $namespace . '\\' . basename($controller, '.php');

    //Do something after controller creation
    do_action_deprecated('Municipio/blade/after_load_controller', $template, '3.0', 'Municipio/blade/afterLoadController'); 

    return new $class();
  }

  /**
   * @param $view
   * @param array $data
   */
  public function renderView($view, $data = array())
  {
    try {
        echo Blade::instance()->make(
            $view,
            array_merge(
              $data,
              array('errorMessage' => false)
            )
        )->render();
    } catch (\Throwable $e) {
        echo Blade::instance()->make(
            '404',
            array_merge(
                $data,
                array(
                  'errorMessage' => $e,
                  'post_type' => null,
                  'heading' => __("Sorry! ", 'municipio'),
                  'subheading' => __("Something went wrong on this page. If this error is persistent please contact us!", 'municipio'),
                  'debugHeading' => __("Detailed information", 'municipio')
                )
            )
        )->render();
    }

    return false;
  }

  /**
   * Get a view clean view path
   * @param  string $view The view path
   * @return void
   */
  private function getViewNameFromPath($view) {
    //Remove all paths
    $view = str_replace(
              \Municipio\Helper\Template::getViewPaths(), 
              "", 
              $view
            ); // Remove view path
    
    //Remove suffix
    $view = trim(str_replace(".blade.php", "", $view), "/");
    
    return str_replace("/", ".", $view); 
  }

  /**
   * Get a controller name
   * @param  string $view The view path
   * @return void
   */
  private function getControllerNameFromView($view) {
    return str_replace(".", "", ucwords($view)); 
  }

  /**
   * Filter template name (what to look for)
   * @return string
   */
  public function addTemplateFilters()
  {
      $types = array(
          'index'      => 'index.blade.php',
          'home'       => 'archive.blade.php',
          'single'     => 'single.blade.php',
          'page'       => 'page.blade.php',
          '404'        => '404.blade.php',
          'archive'    => 'archive.blade.php',
          'author'     => 'author.blade.php',
          'category'   => 'category.blade.php',
          'tag'        => 'tag.blade.php',
          'taxonomy'   => 'taxonomy.blade.php',
          'date'       => 'date.blade.php',
          'front-page' => 'front-page.blade.php',
          'paged'      => 'paged.blade.php',
          'search'     => 'search.blade.php',
          'single'     => 'single.blade.php',
          'singular'   => 'singular.blade.php',
          'attachment' => 'attachment.blade.php',
      );

      $types = apply_filters_deprecated('Municipio/blade/template_types', [$types], '3.0', 'Municipio/blade/templateTypes'); 

      if (isset($types) && !empty($types) && is_array($types)) {
        foreach ($types as $key => $type) {
          add_filter($key . '_template', function ($original) use ($key, $type, $types) {
            
            //Fron page
            if (empty($original) && is_front_page()) {
              $type = $types['front-page'];
            }

            $templatePath = \Municipio\Helper\Template::locateTemplate($type);

            // Look for post type archive
            global $wp_query;
            if (is_post_type_archive() && isset($wp_query->query['post_type'])) {
              $search = 'archive-' . $wp_query->query['post_type'] . '.blade.php';

              if ($found = \Municipio\Helper\Template::locateTemplate($search)) {
                $templatePath = $found;
              }
            }

            // Look for post type single page
            if (is_single() && isset($wp_query->query['post_type'])) {
              $search = 'single-' . $wp_query->query['post_type'] . '.blade.php';
              if ($found = \Municipio\Helper\Template::locateTemplate($search)) {
                $templatePath = $found;
              }
            }

            // Transformation made 
            if ($templatePath) {
              return $templatePath;
            }

            // No changes needed
            return $original;

          });
        }
      }
    }

  /**
   * Proxy for accessing provate props
   * @return mixed Array of values
   */
  public function accessProtected($obj, $prop) {
      $reflection = new \ReflectionClass($obj);
      $property = $reflection->getProperty($prop);
      $property->setAccessible(true);
      return $property->getValue($obj);
  }
}