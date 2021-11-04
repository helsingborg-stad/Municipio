<?php 

namespace Municipio;

/**
 * Class App
 * @package Municipio
 */
class Upgrade
{
  private $dbVersion = 5; //The db version we want to achive 
  private $dbVersionKey = 'municipio_db_version'; 
  private $db; 

  /**
   * App constructor.
   */
  public function __construct()
  {
    add_action('init', array($this, 'initUpgrade')); 
    //add_action('init', array($this, 'debug')); 
  }

  /**
   * Enable to print stuff you need.
   *
   * @return void
   */
  public function debug() {
    var_dump(get_theme_mods()); 
  }

  /**
   * Upgrade database,
   * when you want to upgrade database, 
   * create a new function and increase 
   * $this->dbVersion. 
   * 
   * Method inspiration from WordPress Core. 
   *
   * @return boolean
   */
  private function v_1($db) : bool {
    //update code here
    //var_dump(get_theme_mods()); //A gate way to start!
    return true; //Return false to keep running this each time! 
  }

  // Migrate width from acf to kirki
  private function v_5($db) : bool {
    
    //Move
    $this->migrateThemeMod('widths', 'container', 'field_609bdcc8348d6');
    $this->migrateThemeMod('widths', 'container_frontpage', 'field_60928f237c070');
    $this->migrateThemeMod('widths', 'container_archive', 'field_609bdcad348d5');
    $this->migrateThemeMod('widths', 'container_content', 'field_609298276e5b2');

    $this->migrateThemeMod('widths', 'column_size_left', 'field_60d339b60049e');
    $this->migrateThemeMod('widths', 'column_size_right', 'field_60d3393d1231a');

    $this->deleteThemeMod('widths');
    
    return true; 
  }

  //Migrate navigation position. TODO: TEST!
  private function v_6() : bool {

    $this->migrateThemeMod('general', 'secondary_navigation_position', 'field_60cb4dd897cb8');
    $this->deleteThemeMod('general');

    return true; 
  }

  //Migrate radius. TODO: TEST!
  private function v_7() : bool {

    $this->migrateThemeMod('radius', 'radius_xs', 'field_603662f7a16f8');
    $this->migrateThemeMod('radius', 'radius_sm', 'field_6038fa31cfac6');
    $this->migrateThemeMod('radius', 'radius_md', 'field_6038fa400384b');
    $this->migrateThemeMod('radius', 'radius_lg', 'field_6038fa52576ba');

    $this->deleteThemeMod('radius');

    return true; 
  }

  //Migrate header stuff. TODO: TEST!
  private function v_8() : bool {

    $this->migrateThemeMod('header', 'header_sticky', 'field_61434d3478ef7');
    $this->migrateThemeMod('header', 'header_background', 'field_61446365d1c7e');
    $this->migrateThemeMod('header', 'header_color', 'field_614467575de00');
    $this->migrateThemeMod('header', 'header_modifier', 'field_6070186956c15');
    
    $this->deleteThemeMod('header');

    return true; 
  }

  //Migrate header. TODO: TEST!
  private function v_9() : bool {

    $this->migrateThemeMod('padding', 'main_content_padding', 'field_611e43ec4dfa5');
    
    $this->deleteThemeMod('padding');

    return true; 
  }

  //Migrate quicklinks stuff. TODO: TEST!
  private function v_10() : bool {

    $this->migrateThemeMod('quicklinks', 'quicklinks_background_type', 'field_61570dd479d9b');
    $this->migrateThemeMod('quicklinks', 'quicklinks_custom_background', 'field_61570e6979d9c');
    $this->migrateThemeMod('quicklinks', 'quicklinks_background', 'field_6123844e0f0bb');
    $this->migrateThemeMod('quicklinks', 'quicklinks_color', 'field_6127571bcc76e');
    $this->migrateThemeMod('quicklinks', 'quicklinks_sticky', 'field_61488b616937c');
    $this->migrateThemeMod('quicklinks', 'quicklinks_location', 'field_61488c4f6b4fd');
    
    $this->deleteThemeMod('quicklinks');

    return true; 
  }

  //Migrate quicklinks stuff. TODO: TEST!
  private function v_11() : bool {

    $overlays = get_theme_mod('hero'); 

    $defaultColor = $overlays['field_614c713ae73ea']['field_614c7189e73eb']; 
    $defaultOpacity = $overlays['field_614c713ae73ea']['field_614c7197e73ec']; 

    $vibrantColor = $overlays['field_614c720fb65a4']['field_614c720fb65a5'];
    $vibrantOpacity = $overlays['field_614c720fb65a4']['field_614c720fb65a6']; 

    $defaultOverlay = $this->hex2rgba($defaultColor, "0.".(int)$defaultOpacity); 
    $vibrantOverlay = $this->hex2rgba($vibrantColor, "0.".(int)$vibrantOpacity); 

    set_theme_mod('hero_overlay_neutral', $defaultOverlay);
    set_theme_mod('hero_overlay_vibrant', $vibrantOverlay);

    $this->deleteThemeMod('overlay'); 

    return false; 
  }

  /**
   * Move and clean out the old theme mod
   *
   * @param string $oldKey
   * @param string $newKey
   * @return bool
   */
  private function migrateThemeMod($oldKey, $newKey, $subkey = null) {
    if($oldValue = get_theme_mod($oldKey)) {
      if($subkey && isset($oldValue[$subkey])) {
        return set_theme_mod($newKey, $oldValue[$subkey]);
      } elseif(is_null($subkey)) {
        return set_theme_mod($newKey, $oldValue);
      }      
    }
    return false; 
  }

  /**
   * Deletes a theme mod
   *
   * @param string $key
   * @return bool
   */
  private function deleteThemeMod($key) {
    return remove_theme_mod($key);
  }

  /**
   * Undocumented function
   *
   * @param [type] $color
   * @param boolean $opacity
   * @return void
   */
  private function hex2rgba($color, $opacity = false) {
 
    $default = 'rgb(0,0,0)';
   
    //Return default if no color provided
    if(empty($color)) {
      return $default; 
    }
   
    //Sanitize $color if "#" is provided 
    if ($color[0] == '#' ) {
      $color = substr( $color, 1 );
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    } elseif ( strlen( $color ) == 3 ) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    } else {
            return $default;
    }

    //Convert hexadec to rgb
    $rgb =  array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if($opacity){
      if(abs($opacity) > 1) {
        $opacity = 1.0;
      }
      $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
      $output = 'rgb('.implode(",",$rgb).')';
    }

    //Return rgb(a) color string
    return $output;
  }

  /**
   * Run upgrade functions
   *
   * @return void
   */
  public function initUpgrade () {

    $currentDbVersion = is_numeric(get_option($this->dbVersionKey)) ? (int) get_option($this->dbVersionKey) : 1; 

    if($this->dbVersion != $currentDbVersion) {

      if(!is_numeric($this->dbVersion)) {
        wp_die(__('To be installed database version must be a number.', 'municipio')); 
      }

      if(!is_numeric($currentDbVersion)) {
        wp_die(__('Current database version must be a number.', 'municipio')); 
      }

      if($currentDbVersion > $this->dbVersion) {
        wp_die(__('Database cannot be lower than currently installed (cannot downgrade).', 'municipio')); 
      }

      //Fetch global wpdb object, save to $db
      $this->globalToLocal('wpdb', 'db'); 

      //Run upgrade(s)
      while($currentDbVersion <= $this->dbVersion) {
        $currentDbVersion++; 
        $funcName = 'v_' . (string) $currentDbVersion; 
        if(method_exists($this, $funcName)) {
          if($this->{$funcName}($this->db)) {
            update_option($this->dbVersionKey, (int) $currentDbVersion); 
            wp_cache_flush(); 
          }
        }
      }
    }
  }

  /**
   * Creates a local copy of the global instance
   * The target var should be defined in class header as private or public
   * @param string $global The name of global varable that should be made local
   * @param string $local Handle the global with the name of this string locally
   * @return void
   */
  private function globalToLocal($global, $local = null)
  {
      global $$global;

      if(is_null($$global)) {
          return false;
      }

      if (is_null($local)) {
          $this->$global = $$global;
      } else {
          $this->$local = $$global;
      }

      return true;
  }
}