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